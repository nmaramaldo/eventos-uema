<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'eventos';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'coordenador_id',
        'nome',
        'descricao',
        'data_inicio_evento',
        'data_fim_evento',
        'data_inicio_inscricao',
        'data_fim_inscricao',
        'tipo_evento',
        'logomarca_url',
        'status',
    ];

    public function detalhes() 
    {
        return $this->hasMany(EventoDetalhe::class, 'evento_id', 'id');
    }

    public function coordenador() 
    {
        return $this->belongsTo(User::class, 'coordenador_id', 'id');
    }

    public function inscricoes() 
    {
        return $this->hasMany(Inscricao::class, 'evento_id', 'id');
    }
}

