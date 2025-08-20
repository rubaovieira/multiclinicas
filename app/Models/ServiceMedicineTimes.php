<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ServiceMedicineTimes extends Model
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
        'service_medicine_id',
        'time',
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

    public function serviceMedicine()
    {
        return $this->belongsTo(ServiceMedicine::class, 'service_medicine_id');
    }

    public function serviceMedicineTimeMinistereds()
    {
        return $this->hasMany(ServiceMedicineTimeMinistereds::class, 'service_medicine_time_id')
        ->whereDate('created_at', Carbon::today());
    }

    public function serviceMedicineTimeMinisteredsAll()
    {
        return $this->hasMany(ServiceMedicineTimeMinistereds::class, 'service_medicine_time_id');
    }
    
}