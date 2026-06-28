<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncBandingBali extends Command
{
    // Tambahkan flag --init untuk penarikan awal semua data
    protected $signature = 'sync:banding-bali {--init : Tarik semua data dari awal tahun (Inisialisasi)}';
    protected $description = 'Sinkronisasi perkara banding se-Bali ke DB Terpusat';

    public function handle()
    {
        $list_pn = [
            ['id' => '099820', 'nama' => 'PN Tabanan', 'conn' => 'pn_tabanan'],
            ['id' => '099780', 'nama' => 'PN Denpasar', 'conn' => 'pn_denpasar'],
            ['id' => '099794', 'nama' => 'PN Singaraja', 'conn' => 'pn_singaraja'],
            ['id' => '099841', 'nama' => 'PN Gianyar', 'conn' => 'pn_gianyar'],
            ['id' => '099816', 'nama' => 'PN Semarapura', 'conn' => 'pn_semarapura'],
            ['id' => '099858', 'nama' => 'PN Bangli', 'conn' => 'pn_bangli'],
            ['id' => '099837', 'nama' => 'PN Amlapura', 'conn' => 'pn_amlapura'],
            ['id' => '099802', 'nama' => 'PN Negara', 'conn' => 'pn_negara'],
        ];


        foreach ($list_pn as $pn) {
            $this->warn("--- Memproses: {$pn['nama']} ---");

            try {
                // Menghitung total data untuk progress bar
                $totalData = DB::connection($pn['conn'])->table('perkara_banding')->count();
                $bar = $this->output->createProgressBar($totalData);

                DB::connection($pn['conn'])->table('perkara_banding')
                    ->select(
                        'perkara_banding.*',
                        DB::raw('IF(ecourt_banding.nomor_perkara IS NOT NULL, 1, 0) as ecourt'),
                        DB::raw('IF(berpadu_pelimpahan_register.nomor_perkara IS NOT NULL, 1, 0) as eberpadu'),

                    )
                    ->leftJoin('ecourt_banding', 'perkara_banding.nomor_perkara_pn', '=', 'ecourt_banding.nomor_perkara')
                    ->leftJoin('berpadu_pelimpahan_register', 'perkara_banding.nomor_perkara_pn', '=', 'berpadu_pelimpahan_register.nomor_perkara')

                    ->when(!$this->option('init'), function ($query) {
                        // Jika BUKAN init (rutin harian), ambil yang diupdate dari awal tahun 
                        // ATAU yang putusan PT-nya masih kosong (perkara masih jalan)
                        return $query->where(function ($q) {
                            $q->where('diperbaharui_tanggal', '>=', now()->startOfYear())
                                ->orWhereNull('putusan_banding');
                        });
                    })
                    ->orderBy('perkara_id')
                    ->chunk(500, function ($rows) use ($pn, $bar) {
                        $batch = [];
                        foreach ($rows as $row) {
                            $data = (array) $row;
                            $data['pn_id'] = $pn['id'];

                            // Bersihkan nilai '0000-00-00' yang sering ada di SIPP (Penyebab Error MySQL 1292)
                            foreach ($data as $key => $value) {
                                if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
                                    $data[$key] = null;
                                }
                            }

                            $batch[] = $data;
                        }

                        if (!empty($batch)) {
                            // Ambil semua kolom untuk instruksi UPDATE jika data sudah ada
                            $columns = array_keys($batch[0]);

                            DB::table('perkara_banding_terpusat')
                                ->upsert($batch, ['pn_id', 'perkara_id'], $columns);
                        }

                        $bar->advance(count($rows));
                    });


                $bar->finish();
                $this->info("\nSukses sinkron {$pn['nama']}.");

                Http::timeout(5)
                    ->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                        'chat_id' => env('TELEGRAM_CHAT_ID'),
                        'text'    => "*Laravel Cron Success*\n\n" .
                            "*Message:* " . $pn['nama'] . " sukses disinkron \n",


                    ]);
            } catch (\Exception $e) {
                $errorMessage = "Sync Error {$pn['nama']}: " . $e->getMessage();
                $this->error("\n" . $errorMessage);
                Log::error($errorMessage, ['exception' => $e]);
                Http::timeout(5)
                    ->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                        'chat_id' => env('TELEGRAM_CHAT_ID'),
                        'text'    => "❌ *Laravel Cron Error*\n\n" .
                            "*Message:* " . $pn['nama'] . " gagal disinkron \n" .
                            "*Error:* " . $e->getMessage(),

                    ]);
            }
        }

        $this->info("\nProses Selesai.");
    }
}
