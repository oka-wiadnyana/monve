<?php

namespace App\Filament\Resources\BackupTables\Tables;

use App\Models\BackupTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BackupTablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('SCHEMA_NAME')
                    ->label('Database PN')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('DEFAULT_COLLATION_NAME')
                    ->label('Collation')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Menampilkan info update terakhir (diambil dari tabel tables)
                TextColumn::make('last_update_banding')
                    ->label('Sync Terakhir Table Banding')
                    ->state(function ($record) {
                        // Kita gunakan tabel 'perkara' sebagai benchmark utama database tersebut
                        return cache()->remember("sync_{$record->SCHEMA_NAME}_banding", 60, function () use ($record) {
                            $kolomDinamis = ['diinput_tanggal', 'tgl_st_asli', 'diperbaharui_tanggal'];

                            foreach ($kolomDinamis as $kolom) {
                                try {
                                    $date = DB::connection('mysql_backup')
                                        ->table($record->SCHEMA_NAME . '.perkara_banding')
                                        ->max($kolom);

                                    if ($date) return $date;
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            return null;
                        });
                    })
                    ->dateTime()
                    ->sortable()
                    ->color(
                        fn($state) =>
                        $state && \Carbon\Carbon::parse($state)->isToday() ? 'success' : 'danger'
                    ),
                TextColumn::make('last_update_perkara')
                    ->label('Sync Terakhir Perkara')
                    ->state(function ($record) {
                        // Kita gunakan tabel 'perkara' sebagai benchmark utama database tersebut
                        return cache()->remember("sync_{$record->SCHEMA_NAME}", 60, function () use ($record) {
                            $kolomDinamis = ['diinput_tanggal', 'tgl_st_asli', 'tanggal_permohonan'];

                            foreach ($kolomDinamis as $kolom) {
                                try {
                                    $date = DB::connection('mysql_backup')
                                        ->table($record->SCHEMA_NAME . '.perkara')
                                        ->max($kolom);

                                    if ($date) return $date;
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            return null;
                        });
                    })
                    ->dateTime()
                    ->sortable()
                    ->color(
                        fn($state) =>
                        $state && \Carbon\Carbon::parse($state)->isToday() ? 'success' : 'danger'
                    ),
            ])
            ->filters([
                // Filter otomatis agar yang muncul cuma sipp_pn_
                Filter::make('Hanya PN')
                    ->query(fn($query) => $query->where('SCHEMA_NAME', 'like', 'sipp_pn_%'))
                    ->default(),
            ])
            ->headerActions([
                // CreateAction::make(),
            ])
            ->actions([
                // EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
