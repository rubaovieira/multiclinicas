<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfigSchedule extends Model
{
    protected $table = 'user_config_schedules';
    protected $fillable = ['user_id', 'active'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(UserSchedule::class, 'user_config_schedule_id');
    }
} 