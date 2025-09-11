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
        'coodernador_id',
        'nome',
        'descricao',
        'data_inicio_evento',
        'data_fim_evento',
        'data_inicio_inscricao',
        'data_fim_incricao',
        'tipo_evento',
        'logomarca_url',
        'status',
    ];

    public function detalhes() 
    {
        return $this->hasMany(EventoDetalhe::class, 'evento_id', 'id');
    }

    public function coodernador() 
    {
        return $this->belongsTo(User::class, 'coordenador_id', 'id');
    }
}

