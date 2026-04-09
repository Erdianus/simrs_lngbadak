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
        Schema::table('eslons', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('nama');
        });
        Schema::table('layanans', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('nama');
        });
        Schema::table('sub_layanans', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('nama');
        });
        Schema::table('sp3s', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('no_sp3');
        });
        Schema::table('billings', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('sp3_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eslons', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('layanans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('sub_layanans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('sp3s', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
