<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SyncManager extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected  string $view = 'filament.pages.sync-manager';
    protected static ?string $title = 'Manajer Sinkronisasi Database';

    public $search = '';

    public function getStats(): array
    {
        $data = Cache::remember('sipp_sync_data', 3600, function () {
            set_time_limit(300);
            $comparison = [];
            $backupDbName = 'sipp_pn_tab';
            $localDbKey = "Tables_in_sipp_clone";

            $allTables = DB::connection('sipp_local')->select('SHOW TABLES');

            foreach ($allTables as $table) {
                $tableName = $table->$localDbKey;
                $countLocal = DB::connection('sipp_local')->table($tableName)->count();

                try {
                    $countBackup = DB::connection('mysql_backup')
                        ->table($backupDbName . '.' . $tableName)
                        ->count();
                    $existsInTarget = true;
                } catch (\Exception $e) {
                    $countBackup = 0;
                    $existsInTarget = false;
                }

                if ($countLocal > 0 || $countBackup > 0) {
                    $comparison[] = [
                        'name' => $tableName,
                        'local' => $countLocal,
                        'backup' => $countBackup,
                        'gap' => $countLocal - $countBackup,
                        'exists_in_target' => $existsInTarget
                    ];
                }
            }
            return $comparison;
        });

        if (!empty($this->search)) {
            $data = array_filter($data, function ($item) {
                return str_contains(strtolower($item['name']), strtolower($this->search));
            });
        }

        return $data;
    }

    public function refreshData()
    {
        Cache::forget('sipp_sync_data');
        Notification::make()->title('Data berhasil diperbarui (100% Akurat)')->success()->send();
    }

    // Fungsi untuk Sinkron SEMUA tabel yang ada selisih
    // Tambahkan ini di bagian atas class
    public array $syncErrors = [];

    public function syncAll()
    {
        $this->syncErrors = []; // Reset catatan eror
        $stats = $this->getStats();
        $syncedCount = 0;
        $errorCount = 0;

        foreach ($stats as $stat) {
            if ($stat['exists_in_target'] && $stat['gap'] > 0) {
                // Kita modifikasi sedikit agar fungsi syncTable mengembalikan true/false
                $success = $this->syncTable($stat['name'], false);

                if ($success) {
                    $syncedCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        $this->refreshData();

        if ($errorCount > 0) {
            Notification::make()
                ->title("Sinkronisasi Selesai dengan $errorCount Eror")
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title("$syncedCount tabel berhasil disinkronkan")
                ->success()
                ->send();
        }
    }

    public function syncTable($tableName, $shouldRefresh = true)
    {
        try {
            $data = DB::connection('sipp_local')->table($tableName)->get();
            if ($data->isEmpty()) return true;

            $columns = Schema::connection('sipp_local')->getColumnListing($tableName);
            $primaryKey = in_array('perkara_id', $columns) ? 'perkara_id' : (in_array('id', $columns) ? 'id' : $columns[0]);

            foreach ($data->chunk(500) as $chunk) {
                $items = $chunk->map(fn($item) => (array) $item)->toArray();
                DB::connection('mysql_backup')->table($tableName)->upsert($items, [$primaryKey], array_keys($items[0]));
            }

            if ($shouldRefresh) $this->refreshData();
            return true;
        } catch (\Exception $e) {
            // Catat erornya ke dalam array
            $this->syncErrors[] = [
                'table' => $tableName,
                'message' => $e->getMessage()
            ];
            return false;
        }
    }
}
