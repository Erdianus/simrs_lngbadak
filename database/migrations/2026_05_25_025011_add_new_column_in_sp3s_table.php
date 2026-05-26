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
        Schema::table('sp3s', function (Blueprint $table) {
            $table->tinyInteger('revisi')->default(0)->nullable();
            $table->string('alasan_rev')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sp3s', function (Blueprint $table) {
            $table->dropColumn(['revisi', 'alasan_rev']);
        });
    }
};
