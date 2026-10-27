<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->unsignedBigInteger('kasus_id');
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->unsignedBigInteger('jenis_pelanggaran_id');
            $table->string('nama_pelanggaran');
            $table->integer('poin');
            $table->string('kelas');
            $table->text('pasal');
            $table->softDeletes();
            $table->text('alasan_hapus')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('kasus_id')
                ->references('id')
                ->on('kasus')
                ->onDelete('restrict');

            $table->foreign('siswa_id')
                ->references('id')
                ->on('siswa')
                ->onDelete('restrict');

            $table->foreign('tahun_ajaran_id')
                ->references('id')
                ->on('tahun_ajaran')
                ->onDelete('restrict');

            $table->foreign('jenis_pelanggaran_id')
                ->references('id')
                ->on('jenis_pelanggaran')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};
