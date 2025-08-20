<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receita extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'receitas';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'service_id',
        'receita_text',
        'active',
        'created_by',
        'deleted_by'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Relacionamento com o serviço
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Relacionamento com o usuário que criou
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relacionamento com o usuário que deletou
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
