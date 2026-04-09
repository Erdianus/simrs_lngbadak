<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sp3 extends Model
{
    use HasFactory;

    protected $table = 'sp3s';

    protected $fillable = [
        'no_sp3',
        'keterangan',
        'is_approved_by_verifikator',
        'is_approved_by_keuangan',
        'slug'
    ];

    protected $appends = [
        'total_biaya'
    ];

    protected static function booted()
    {
        static::creating(function (Sp3 $sp3) {
            if ($sp3->no_sp3) {
                $sp3->slug = $sp3->slug ?: static::generateUniqueSlug($sp3->no_sp3);
            }
        });

        static::updating(function (Sp3 $sp3) {
            if (($sp3->isDirty('no_sp3') && $sp3->no_sp3) || empty($sp3->slug)) {
                if ($sp3->no_sp3) {
                    $sp3->slug = static::generateUniqueSlug($sp3->no_sp3, $sp3->id);
                }
            }
        });
    }

    protected static function generateUniqueSlug(string $value, int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTotalBiayaAttribute()
    {
        return $this->billings()->sum('biaya');
    }

    public function billings(){
        return $this->hasMany(Billing::class, 'sp3_id','id');
    }


}
