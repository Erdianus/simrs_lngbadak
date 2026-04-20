<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiEmbalaceSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';
    protected $table = 'transaksi_embalace';
}
