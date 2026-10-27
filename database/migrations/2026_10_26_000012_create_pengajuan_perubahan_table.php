<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_perubahan', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->unsignedBigInteger('pelanggaran_id');
            $table->unsignedBigInteger('diajukan_oleh');
            $table->text('alasan');
            $table->json('data_lama');
            $table->json('data_baru');
            $table->string('status')->default('menunggu');
            $table->unsignedBigInteger('diputuskan_oleh')->nullable();
            $table->text('catatan_keputusan')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('pelanggaran_id')
                ->references('id')
                ->on('pelanggaran')
                ->onDelete('restrict');

            $table->foreign('diajukan_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('diputuskan_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perubahan');
    }
};
