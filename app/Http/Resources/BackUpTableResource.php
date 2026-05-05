<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BackUpTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lastUpdate = cache()->remember("sync_{$this->SCHEMA_NAME}", 60, function () {
            $kolomDinamis = ['diinput_tanggal', 'tgl_st_asli'];
            foreach ($kolomDinamis as $kolom) {
                try {
                    $date = \Illuminate\Support\Facades\DB::connection('mysql_backup')
                        ->table($this->SCHEMA_NAME . '.perkara')
                        ->max($kolom);
                    if ($date) return $date;
                } catch (\Exception $e) {
                    continue;
                }
            }
            return null;
        });

        return [
            'database_name' => $this->SCHEMA_NAME,
            'collation'     => $this->DEFAULT_COLLATION_NAME,
            'last_sync'     => $lastUpdate,
            'is_active'     => $lastUpdate ? \Illuminate\Support\Carbon::parse($lastUpdate)->isToday() : false,
            // Opsional: Jika ingin menyertakan detail tabel di dalamnya
            'details_count' => $this->whenLoaded('tables', function () {
                return $this->tables->count();
            }),
            'table_details' => BackUpTableDetailResource::collection($this->whenLoaded('tables')),
        ];
    }
}
