<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DbStatusWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getConnectionStat('sipp_local', 'Database SIPP Lokal (Asal)'),
            $this->getConnectionStat('mysql_backup', 'Database Backup (Tujuan)'),
        ];
    }

    private function getConnectionStat(string $connection, string $label): Stat
    {
        // DB::connection($connection)->getPdo();
        try {
            // Ambil info host dan user dari config
            $host = Config::get("database.connections.{$connection}.host");
            $user = Config::get("database.connections.{$connection}.username");
            $dbName = Config::get("database.connections.{$connection}.database");

            DB::connection($connection)->getPdo();
            // Tes koneksi dengan query ringan

            return Stat::make($label, "ONLINE")
                ->description("{$user}@{$host} | DB: {$dbName}")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success');
        } catch (\Exception $e) {
            return Stat::make($label, "OFFLINE")
                ->description("Gagal terhubung ke {$host}")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger');
        }
    }
}
