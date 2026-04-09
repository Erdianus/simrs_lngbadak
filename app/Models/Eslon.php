<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Eslon extends Model
{
    use HasFactory;

    protected $table = 'eslons';

    protected $fillable = [
        'nama',
        'deskripsi',
        'slug'
    ];

    protected static function booted()
    {
        static::creating(function (Eslon $eslon) {
            $eslon->slug = $eslon->slug ?: static::generateUniqueSlug($eslon->nama);
        });

        static::updating(function (Eslon $eslon) {
            if ($eslon->isDirty('nama') || empty($eslon->slug)) {
                $eslon->slug = static::generateUniqueSlug($eslon->nama, $eslon->id);
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

    public function sp3s(){
        return $this->hasMany(Sp3::class, 'eslon_id', 'id');
    }

}
