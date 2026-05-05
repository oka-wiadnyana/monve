<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perkara_banding_terpusat', function (Blueprint $table) {
            $table->integer('pn_id')->comment('ID/Kode PN asal (e.g., 1 untuk Tabanan, 2 Denpasar, dst)');
            $table->bigInteger('perkara_id')->unsigned()->comment('Id Perkara asli dari SIPP PN');

            // --- Data Perkara PN ---
            $table->char('jenis_banding', 1)->default('1')->comment('1:akhir;2:sela');
            $table->integer('alur_perkara_id')->unsigned();
            $table->string('nomor_perkara_pn', 250);
            $table->date('putusan_pn')->nullable();
            $table->bigInteger('pihak_pembanding')->nullable();
            $table->date('permohonan_banding')->nullable();
            $table->longText('pemohon_banding')->nullable();
            $table->longText('para_pihak')->nullable();

            // --- Pemberitahuan & Memori ---
            $table->date('pemberitahuan_putusan_pn')->nullable();
            $table->date('pemberitahuan_permohonan_banding')->nullable();
            $table->date('penerimaan_memori_banding')->nullable();
            $table->date('penyerahan_memori_banding')->nullable();
            $table->date('penerimaan_kontra_banding')->nullable();
            $table->date('penyerahan_kontra_banding')->nullable();

            // --- Inzage ---
            $table->date('pemberitahuan_inzage')->nullable();
            $table->date('pemberitahuan_inzage_pembanding')->nullable();
            $table->date('pemberitahuan_inzage_terbanding')->nullable();
            $table->date('pelaksanaan_inzage')->nullable();
            $table->date('pelaksanaan_inzage_pembanding')->nullable();
            $table->date('pelaksanaan_inzage_terbanding')->nullable();

            // --- Pengiriman Berkas ---
            $table->date('pengiriman_berkas_banding')->nullable();
            $table->string('nomor_surat_pengiriman_berkas_banding', 255)->nullable();

            $table->string('surat_pengantar_path', 500)->nullable();
            $table->dateTime('tanggal_kirim')->nullable();
            $table->date('penerimaan_kembali_berkas_banding')->nullable();

            // --- Data Register Banding (PT) ---
            $table->integer('nomor_urut_register')->nullable();
            $table->date('tanggal_pendaftaran_banding')->nullable();
            $table->string('nomor_perkara_banding', 50)->nullable();

            $table->bigInteger('panitera_pembuat_akta_banding')->unsigned()->nullable();

            // --- Majelis Hakim Banding ---
            $table->bigInteger('hakim1_banding_id')->nullable();
            $table->string('hakim1_banding', 255)->nullable();
            $table->bigInteger('hakim2_banding_id')->nullable();
            $table->string('hakim2_banding', 255)->nullable();
            $table->bigInteger('hakim3_banding_id')->nullable();
            $table->string('hakim3_banding', 255)->nullable();
            $table->bigInteger('hakim4_banding_id')->nullable();
            $table->string('hakim4_banding', 255)->nullable();
            $table->bigInteger('hakim5_banding_id')->nullable();
            $table->string('hakim5_banding', 255)->nullable();
            $table->string('majelis_hakim_banding', 500)->nullable();


            // --- Panitera & Sidang PT ---
            $table->bigInteger('panitera_pengganti_banding_id')->nullable();
            $table->string('panitera_pengganti_banding', 255)->nullable();

            $table->date('tanggal_penetapan_sidang_pertama')->nullable();
            $table->date('tanggal_sidang_pertama')->nullable();

            // --- Putusan Banding ---
            $table->date('putusan_banding')->nullable();
            $table->integer('sumber_hukum_id')->unsigned()->nullable();
            $table->integer('status_putusan_banding_id')->unsigned()->nullable();
            $table->string('status_putusan_banding_text', 50)->nullable();
            $table->string('nomor_putusan_banding', 255)->nullable();

            $table->longText('amar_putusan_banding')->nullable();
            $table->string('amar_putusan_banding_dok', 250)->nullable();


            // --- Pengiriman Salinan & Minutasi ---
            $table->date('tgl_kirim_salinan_putusan')->nullable();
            $table->date('minutasi_banding')->nullable();
            $table->date('tgl_minutasi')->nullable();
            $table->date('tgl_pengiriman_berkas_putusan')->nullable();

            // --- Pemberitahuan Putusan Banding ---
            $table->date('pemberitahuan_putusan_banding')->nullable();
            $table->date('pemberitahuan_putusan_banding_pembanding')->nullable();
            $table->date('pemberitahuan_putusan_banding_terbanding')->nullable();
            $table->date('tgl_pemberitahuan_putusan')->nullable();

            // --- Lain-lain ---
            $table->text('catatan_banding')->nullable();
            $table->tinyInteger('prodeo_banding')->nullable()->default(0);
            $table->integer('status_banding_id')->unsigned()->nullable();

            $table->string('status_banding_text', 100)->nullable();

            $table->date('tanggal_cabut')->nullable();

            // --- Audit Columns (SIPP) ---
            $table->string('diedit_oleh', 30)->nullable();
            $table->dateTime('diedit_tanggal')->nullable();
            $table->string('diinput_oleh', 30)->nullable();
            $table->dateTime('diinput_tanggal')->nullable();
            $table->string('diperbaharui_oleh', 30)->nullable();
            $table->dateTime('diperbaharui_tanggal')->nullable();


            // --- Audit Columns (Sistem Pusat) ---
            $table->timestamp('terakhir_sinkronisasi')->useCurrent()->useCurrentOnUpdate();

            // --- Indexes & Constraints ---
            // Gabungan pn_id dan perkara_id memastikan tidak ada data duplikat antar pengadilan
            $table->unique(['pn_id', 'perkara_id'], 'unique_perkara_banding_pn');

            // Index untuk kecepatan pencarian nomor perkara dan filter wilayah
            $table->index('nomor_perkara_pn');
            $table->index('nomor_perkara_banding');
            $table->index('pn_id');
            $table->index('diperbaharui_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkara_banding_terpusat');
    }
};
