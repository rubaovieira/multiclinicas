<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceMedicine extends Model
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
        'service_id',
        'medicamento',
        'observation', 
        'deleted_at',
        'deleted_by',
        'start_time',
        'posology',
        'product_id',
        'active'
    ];  
   
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) { 
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
            $model->created_by = Auth::id(); 
        });
    }   
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceMedicineTimes()
    {
        return $this->hasMany(ServiceMedicineTimes::class, 'service_medicine_id')->orderBy('time', 'asc');

    }

    public function serviceMedicineTimeMinistereds()
    {
        return $this->hasMany(ServiceMedicineTimeMinistereds::class, 'service_medicine_time_id');
    }
    
}