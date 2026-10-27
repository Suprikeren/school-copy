<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran_pasal', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_pelanggaran_id');
            $table->unsignedBigInteger('pasal_id');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->primary(['jenis_pelanggaran_id', 'pasal_id']);

            $table->foreign('jenis_pelanggaran_id')
                ->references('id')
                ->on('jenis_pelanggaran')
                ->onDelete('cascade');

            $table->foreign('pasal_id')
                ->references('id')
                ->on('pasal_tatib')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran_pasal');
    }
};
