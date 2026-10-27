<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->primary('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('aksi');
            $table->string('tabel');
            $table->unsignedBigInteger('data_id')->nullable();
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
