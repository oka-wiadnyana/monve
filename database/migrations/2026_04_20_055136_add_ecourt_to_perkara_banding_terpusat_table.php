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
            $table->boolean('ecourt')->default(0);
            $table->boolean('eberpadu')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perkara_banding_terpusat', function (Blueprint $table) {
            $table->dropColumn('ecourt');
            $table->dropColumn('eberpadu');
        });
    }
};
