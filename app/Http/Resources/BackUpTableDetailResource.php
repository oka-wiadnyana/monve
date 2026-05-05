<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class BackUpTableDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $db = $this->TABLE_SCHEMA;
        $tabel = $this->TABLE_NAME;

        $lastUpdate = cache()->remember("last_update_{$db}_{$tabel}", 300, function () use ($db, $tabel) {
            $tanggalKolom = ['diperbaharui_tanggal', 'diinput_tanggal', 'tgl_st_asli', 'tanggal_permohonan', 'tanggal_permohonan_banding'];
            foreach ($tanggalKolom as $kolom) {
                try {
                    $date = DB::connection('mysql_backup')->table("{$db}.{$tabel}")->max($kolom);
                    if ($date) return $date;
                } catch (\Exception $e) {
                    continue;
                }
            }
            return null;
        });

        return [
            'table_name' => $this->TABLE_NAME,
            'rows'       => (int) $this->TABLE_ROWS,
            'last_data'  => $lastUpdate,
            'is_anomaly' => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate)->year > now()->year : false,
            'is_today'   => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate)->isToday() : false,
        ];
    }
}
