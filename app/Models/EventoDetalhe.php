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

    protected $casts = [
        'data' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
        'capacidade' => 'integer',
    ];

    public function evento()
    {
        return $this->belongsTo(Event::class, 'evento_id', 'id');
    }
}
