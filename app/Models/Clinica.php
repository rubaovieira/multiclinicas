<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Clinica extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'slug',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($clinica) {
            $baseSlug = Str::slug($clinica->nome);
            $slug = $baseSlug;
            $counter = 1;

            while (self::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $clinica->slug = $slug;
        });

        static::updating(function ($clinica) {
            if ($clinica->isDirty('nome')) {
                $baseSlug = Str::slug($clinica->nome);
                $slug = $baseSlug;
                $counter = 1;

                while (self::where('slug', $slug)->where('id', '!=', $clinica->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $clinica->slug = $slug;
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
