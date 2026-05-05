<?php

namespace App\Filament\Resources\BackupTables\RelationManagers;

use App\Filament\Resources\BackupTables\BackupTableResource;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class TablesRelationManager extends RelationManager
{
    protected static string $relationship = 'tables';

    protected static ?string $relatedResource = BackupTableResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('TABLE_NAME')
                    ->label('Nama Tabel')
                    ->searchable(),

                TextColumn::make('TABLE_ROWS')
                    ->label('Baris')
                    ->numeric(),

                // Kolom Sakti: Mencari tanggal terbaru dari dalam data tabel tersebut
                TextColumn::make('real_last_update')
                    ->label('Data Terbaru')
                    ->state(function ($record) {
                        if ($record->TABLE_ROWS <= 0) return null;

                        $db = $record->TABLE_SCHEMA;
                        $tabel = $record->TABLE_NAME;

                        return cache()->remember("last_update_{$db}_{$tabel}", 300, function () use ($db, $tabel) {
                            $tanggalKolom = ['diperbaharui_tanggal', 'diinput_tanggal', 'tgl_st_asli'];
                            foreach ($tanggalKolom as $kolom) {
                                try {
                                    $lastDate = DB::connection('mysql_backup')
                                        ->table("{$db}.{$tabel}")
                                        // HAPUS BARIS ->where(...) AGAR 2030 MUNCUL LAGI
                                        ->max($kolom);

                                    if ($lastDate) return $lastDate;
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            return null;
                        });
                    })
                    ->dateTime()
                    ->description(function ($state) {
                        if (!$state) return null;

                        // Cek jika tahun lebih dari tahun ini (2026)
                        if (Carbon::parse($state)->year > now()->year) {
                            return '⚠️ TANGGAL ANOMALI (CEK JAM SERVER)';
                        }
                        return null;
                    })
                    ->color(function ($state) {
                        if (!$state) return 'gray';

                        $date = Carbon::parse($state);

                        // Jika masa depan, kasih warna Danger (Merah)
                        if ($date->year > now()->year) return 'danger';

                        // Jika hari ini, Hijau
                        return $date->isToday() ? 'success' : 'gray';
                    })
                    ->icon(
                        fn($state) =>
                        $state && Carbon::parse($state)->year > now()->year ? 'heroicon-m-exclamation-triangle' : null
                    )
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Kosongkan jika tidak ingin ada tombol "Create" di atas
            ])
            ->actions([
                // Kosongkan array ini untuk menghilangkan tombol "View" di baris tabel
            ])
            ->bulkActions([
                // Kosongkan jika tidak ingin ada fitur checkbox
            ]);
    }
}
