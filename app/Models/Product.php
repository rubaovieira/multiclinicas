<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
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
        'name',
        'qtd_min',
        'clinica_id'
    ];  
   
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) { 
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
            $model->created_by = Auth::id(); 
            $model->clinica_id = Auth::user()->clinica_id;
        });
    }   


    public function inventoryMovements()
    {
        return $this->hasMany(InventoryControl::class, 'product_id');
    }

    public function getCurrentQuantityAttribute()
    {
        return $this->inventoryMovements()->where('active', 1)->sum('qtd'); 
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }
}