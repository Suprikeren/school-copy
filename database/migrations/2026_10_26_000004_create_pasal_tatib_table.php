<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasal_tatib', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->string('nomor');
            $table->text('isi_ringkas');
            $table->boolean('aktif')->default(true);
            $table->string('slug')->unique();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasal_tatib');
    }
};
