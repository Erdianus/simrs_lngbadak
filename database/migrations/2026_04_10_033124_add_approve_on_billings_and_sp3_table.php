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
        Schema::table('billings', function (Blueprint $table) {
            $table->foreignId('approved_verif_pic_by')->unique()->nullable()->constrained('users');
            $table->foreignId('approved_verif_pws_by')->unique()->nullable()->constrained('users');
            $table->foreignId('approved_verif_wadir_by')->unique()->nullable()->constrained('users');
            $table->foreignId('approved_keu_admin_by')->unique()->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
