<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoDetalhe extends Model
{
    use HasFactory;

    protected $table = 'eventos_detalhes';
    protected $fillable = [
        'evento_id',
        'descricao',
        'data',
        'hora_inicio',
        'hora_fim',
        'modalidade',
        'capacidade',
    ];

    public function evento()
    {
        return $this->belongsTo(Event::class, 'evento_id', 'id');
    }
}

