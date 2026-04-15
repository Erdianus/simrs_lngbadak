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
        Schema::create('sp3s', function (Blueprint $table) {
            $table->id();
            $table->string('no_sp3')->unique()->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_approved_by_verifikator')->default(false);
            $table->boolean('is_approved_by_keuangan')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp3s');
    }
};
