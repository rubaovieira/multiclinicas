<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'appointments';

    protected $fillable = [
        'medico_id',
        'paciente_id',
        'data_hora_inicio',
        'data_hora_fim',
        'tipo',
        'status',
        'link_telemedicina',
        'nome_sala',
        'sala_expira_em'
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
        'sala_expira_em' => 'datetime'
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }
} 