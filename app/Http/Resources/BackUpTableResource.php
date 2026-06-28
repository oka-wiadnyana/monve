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
                    $dateBanding = \Illuminate\Support\Facades\DB::connection('mysql_backup')->table($this->SCHEMA_NAME . '.perkara_banding')
                        ->max($kolom);
                    $dateEksekusi = \Illuminate\Support\Facades\DB::connection('mysql_backup')->table($this->SCHEMA_NAME . '.perkara_eksekusi')
                        ->max($kolom);
                    $dateEcourt = \Illuminate\Support\Facades\DB::connection('mysql_backup')->table($this->SCHEMA_NAME . '.ecourt_banding')
                        ->max($kolom);
                    $dateEberpadu = \Illuminate\Support\Facades\DB::connection('mysql_backup')->table($this->SCHEMA_NAME . '.berpadu_pelimpahan_register')
                        ->max($kolom);


                    if ($date) return ['date_perkara' => $date, 'date_perkara_banding' => $dateBanding, 'date_perkara_eksekusi' => $dateEksekusi, 'date_ecourt' => $dateEcourt, 'date_eberpadu' => $dateEberpadu];
                } catch (\Exception $e) {
                    continue;
                }
            }
            return ['date_perkara' => null, 'date_perkara_banding' => null];
        });
        // dd($lastUpdate['date_perkara']);

        return [
            'database_name' => $this->SCHEMA_NAME,
            'collation'     => $this->DEFAULT_COLLATION_NAME,
            'last_sync'     => $lastUpdate,
            'is_active'     => $lastUpdate['date_perkara'] != null ? \Illuminate\Support\Carbon::parse($lastUpdate['date_perkara'])->isToday() : false,
            // Opsional: Jika ingin menyertakan detail tabel di dalamnya
            'details_count' => $this->whenLoaded('tables', function () {
                return $this->tables->count();
            }),
            'table_details' => BackUpTableDetailResource::collection($this->whenLoaded('tables')),
        ];
    }
}
