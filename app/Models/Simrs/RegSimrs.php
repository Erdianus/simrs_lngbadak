<?php

namespace App\Models\BillingSimrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';

    protected $table = 'reg';
}
