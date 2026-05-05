<?php

namespace App\Filament\Resources\BackupTables;

use App\Filament\Resources\BackupTables\Pages\CreateBackupTable;
use App\Filament\Resources\BackupTables\Pages\EditBackupTable;
use App\Filament\Resources\BackupTables\Pages\ListBackupTables;
use App\Filament\Resources\BackupTables\Pages\ViewBackupTable;
use App\Filament\Resources\BackupTables\Schemas\BackupTableForm;
use App\Filament\Resources\BackupTables\Schemas\BackupTableInfolist;
use App\Filament\Resources\BackupTables\Tables\BackupTablesTable;
use App\Models\BackupTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BackupTableResource extends Resource
{
    protected static ?string $model = BackupTable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BackupTableForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BackupTableInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupTablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TablesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackupTables::route('/'),
            'create' => CreateBackupTable::route('/create'),
            'view' => ViewBackupTable::route('/{record}'),
            'edit' => EditBackupTable::route('/{record}/edit'),
        ];
    }
}
