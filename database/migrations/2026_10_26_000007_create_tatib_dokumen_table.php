<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tatib_dokumen', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->string('nama_file');
            $table->string('path');
            $table->unsignedBigInteger('diunggah_oleh');
            $table->date('tanggal');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('diunggah_oleh')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tatib_dokumen');
    }
};
