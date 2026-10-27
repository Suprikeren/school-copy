<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('sp_siswa_id')->nullable();
            $table->unsignedBigInteger('pelanggaran_id')->nullable();
            $table->string('tindakan');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('status')->default('belum');
            $table->unsignedBigInteger('ditangani_oleh');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')
                ->references('id')
                ->on('siswa')
                ->onDelete('restrict');

            $table->foreign('sp_siswa_id')
                ->references('id')
                ->on('sp_siswa')
                ->onDelete('set null');

            $table->foreign('pelanggaran_id')
                ->references('id')
                ->on('pelanggaran')
                ->onDelete('set null');

            $table->foreign('ditangani_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
