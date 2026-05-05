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
        Schema::create('dirput_pt', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_putusan')->nullable(); // Sebelumnya 'judul'
            $table->string('link', 500)->unique();
            $table->string('tgl_register')->nullable(); // Tanggal Register
            $table->string('tgl_putus')->nullable();    // Tanggal Putus
            $table->string('tgl_upload')->nullable();   // Tanggal Upload
            $table->text('info')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dirput_pt');
    }
};
