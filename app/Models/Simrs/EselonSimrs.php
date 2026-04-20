<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EselonSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';
    protected $table = 'ku_kode_eselon';
}
