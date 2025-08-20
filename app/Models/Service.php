<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Service extends Model
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
        'client_id',
        'status',
        'limit_evolution',
        'diagnostico',
        'clinica_id',
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service_procedures()
    {
        return $this->hasMany(ServiceProcedure::class)->orderBy('created_at');
    }

    public function service_medicines()
    {
        return $this->hasMany(ServiceMedicine::class)->orderBy('created_at');
    }

    public function service_evolutions()
    {
        return $this->hasMany(ServiceEvolution::class)->orderBy('created_at');
    }

    public function service_files()
    {
        return $this->hasMany(ServiceFile::class)->orderBy('created_at');
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }

    // Relacionamento com receitas
    public function receitas()
    {
        return $this->hasMany(Receita::class, 'service_id');
    }

    // Método para obter todas as receitas ativas
    public function activeReceitas()
    {
        return $this->receitas()->where('active', true);
    }


    public function service_items()
    {
        // Filtra os procedimentos ativos
        $procedures = $this->service_procedures->where('active', true);
    
        // Filtra os medicamentos ativos
        $medicines = $this->service_medicines->where('active', true);
    
        // Filtra as evoluções ativas
        $evolutions = $this->service_evolutions->where('active', true);
    
        // Filtra os arquivos ativos
        $files = $this->service_files->where('active', true);
    
        // Filtra as receitas ativas
        $receitas = $this->receitas->where('active', true);
    
        // Junta todos os itens e ordena por 'created_at' (do mais recente para o mais antigo)
        $items = $procedures
            ->merge($medicines)
            ->merge($evolutions)
            ->merge($files)
            ->merge($receitas)
            ->sortByDesc('created_at');
    
        return $items;
    }
}
