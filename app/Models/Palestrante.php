<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Palestrante extends Model
{
    use HasFactory;

    protected $table = 'palestrantes';

    // PK é UUID (string)
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'nome',
        'biografia',
        'foto_url',
        // se futuramente adicionar colunas, inclua-as aqui
        // ex.: 'email', 'cargo', 'mini_bio'
    ];

    /**
     * Gera UUID automaticamente ao criar o registro.
     */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function eventos()
    {
        return $this->belongsToMany(
            Event::class,
            'evento_palestrante',   // tabela pivô
            'palestrante_id',
            'evento_id'
        )->withTimestamps();
    }

    public function atividades()
    {
        return $this->belongsToMany(
            EventoDetalhe::class,
            'evento_detalhe_palestrante', // pivô para atividades (se existir)
            'palestrante_id',
            'evento_detalhe_id'
        )->withTimestamps();
    }
}
