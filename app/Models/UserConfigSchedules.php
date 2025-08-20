<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserConfigSchedules extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */ 
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'user_id',
        'active', 
    ];  
   
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) { 
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            } 
        });
    }  
    
}