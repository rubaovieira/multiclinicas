<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Client extends Model
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
        'telephone',
        'caregiver_responsible',
        'address',
        'date_birth',
        'cpf',
        'diagnosis',
        'health_plan_id',
        'active',
        'clinica_id',
        'created_by',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
            
            // Se o created_by já foi definido (como no caso do registro), não sobrescreve
            if (empty($model->created_by)) {
                $model->created_by = Auth::id() ?? null;
            }
            
            // Se o clinica_id já foi definido (como no caso do registro), não sobrescreve
            if (empty($model->clinica_id)) {
                $model->clinica_id = Auth::user()->clinica_id ?? null;
            }
        });
    }

    public function healthPlan()
    {
        return $this->belongsTo(HealthPlan::class);
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

