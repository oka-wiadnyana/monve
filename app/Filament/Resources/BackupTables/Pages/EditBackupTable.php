<?php

namespace App\Filament\Resources\BackupTables\Pages;

use App\Filament\Resources\BackupTables\BackupTableResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBackupTable extends EditRecord
{
    protected static string $resource = BackupTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
