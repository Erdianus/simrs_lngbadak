<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanans';

    protected $fillable = ['nama','slug'];

    protected static function booted()
    {
        static::creating(function (Layanan $layanan) {
            $layanan->slug = $layanan->slug ?: static::generateUniqueSlug($layanan->nama);
        });

        static::updating(function (Layanan $layanan) {
            if ($layanan->isDirty('nama') || empty($layanan->slug)) {
                $layanan->slug = static::generateUniqueSlug($layanan->nama, $layanan->id);
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

    public function subLayanans(){
        return $this->hasMany(SubLayanan::class,'layanan_id','id');
    }
}
