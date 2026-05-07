<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositKamarSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'deposit_kamar';

    public function registrasi()
    {
        return $this->belongsTo(RegSimrs::class, 'no_reg', 'reg_no');
    }
}
