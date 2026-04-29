<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferensiAdmSimrs extends Model
{
    use HasFactory;

    protected $connection = 'odbc';

    protected $table = 'referensi_adm';

    protected $appends = [
        'deskripsi'
    ];

    public function getDeskripsiAttribute()
    {
        return 'Biaya Administrasi';
    }
}
