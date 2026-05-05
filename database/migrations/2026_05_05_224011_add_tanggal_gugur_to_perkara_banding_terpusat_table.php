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
        Schema::table('perkara_banding_terpusat', function (Blueprint $table) {
            $table->date('tanggal_gugur')->nullable()->after('tanggal_cabut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perkara_banding_terpusat', function (Blueprint $table) {
            $table->dropColumn('tanggal_gugur');
        });
    }
};
