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
            $table->foreignId('approved_verif_pic_by')->nullable()->constrained('users');
            $table->foreignId('approved_verif_pws_by')->nullable()->constrained('users');
            $table->foreignId('approved_verif_wadir_by')->nullable()->constrained('users');
            $table->foreignId('approved_keu_admin_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('approved_verif_pic_by');
            $table->dropColumn('approved_verif_pws_by');
            $table->dropColumn('approved_verif_wadir_by');
            $table->dropColumn('approved_keu_admin_by');
        });
    }
};
