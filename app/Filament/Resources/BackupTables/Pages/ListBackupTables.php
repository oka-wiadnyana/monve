<?php

namespace App\Filament\Resources\BackupTables\Pages;

use App\Filament\Resources\BackupTables\BackupTableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBackupTables extends ListRecords
{
    protected static string $resource = BackupTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
