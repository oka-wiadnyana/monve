<?php

namespace App\Filament\Resources\BackupTables\Pages;

use App\Filament\Resources\BackupTables\BackupTableResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBackupTable extends ViewRecord
{
    protected static string $resource = BackupTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
