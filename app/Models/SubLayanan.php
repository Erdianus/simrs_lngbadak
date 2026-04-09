<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubLayanan extends Model
{
    use HasFactory;
    protected $table = 'sub_layanans';

    protected $fillable = [
        'nama',
        'slug',
        'layanan_id'
    ];

    protected static function booted()
    {
        static::creating(function (SubLayanan $subLayanan) {
            $subLayanan->slug = $subLayanan->slug ?: static::generateUniqueSlug($subLayanan->nama);
        });

        static::updating(function (SubLayanan $subLayanan) {
            if ($subLayanan->isDirty('nama') || empty($subLayanan->slug)) {
                $subLayanan->slug = static::generateUniqueSlug($subLayanan->nama, $subLayanan->id);
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

    public function layanan(){
        return $this->belongsTo(Layanan::class,'layanan_id','id');
    }
}
