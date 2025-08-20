<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSchedule extends Model
{
    protected $table = 'user_schedules';
    protected $fillable = [
        'user_config_schedule_id',
        'day',
        'start',
        'end',
        'turn',
        'active',
        'created_by'
    ];
    public $timestamps = true;

    public function configSchedule()
    {
        return $this->belongsTo(UserConfigSchedule::class, 'user_config_schedule_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 