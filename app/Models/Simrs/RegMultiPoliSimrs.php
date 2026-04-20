<?php

namespace App\Models\Simrs;

use App\Models\Eslon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMultiPoliSimrs extends Model
{
    use HasFactory;
    protected $connection = 'odbc';

    protected $table = 'reg_multi_poli';

    protected $appends = [
        'eselon_id'
    ];

    public function getEselonIdAttribute()
    {
        $eselon = Eslon::select('id')->where('nama', $this->eselon)->first();
        return $eselon->id;
    }
}
