<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMultiPoliSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'reg_multi_poli';
}
