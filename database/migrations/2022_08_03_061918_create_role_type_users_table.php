<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRoleTypeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_type_users', function (Blueprint $table) {
            $table->id();
            $table->string('role_type')->nullable();
            $table->timestamps();
        });

        DB::table('role_type_users')->insert([
            ['role_type' => 'Admin'],
            ['role_type' => 'Super Admin'],
            ['role_type' => 'PIC Verifikator'],
            ['role_type' => 'Pengawas Verifikator'],
            ['role_type' => 'Wakil Direktur'],
            ['role_type' => 'Keuangan Admin'],
            ['role_type' => 'Teachers'],
            ['role_type' => 'Student'],
            ['role_type' => 'Staff'],
            ['role_type' => 'Client'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_type_users');
    }
}
