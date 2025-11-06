<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Programacao extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'programacao';

    // ✅ UUID como chave primária
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'evento_id',
        'titulo',
        'descricao',
        'modalidade',
        'data_hora_inicio',
        'data_hora_fim',
        'localidade',
        'capacidade',
        'requer_inscricao',
        'local_id',
    ];

    protected $casts = [
        'capacidade'        => 'integer',
        'requer_inscricao'  => 'boolean',
        'data_hora_inicio'  => 'datetime',
        'data_hora_fim'     => 'datetime',
    ];

    protected $attributes = [
        'requer_inscricao' => false,
    ];

    // --- RELACIONAMENTOS ---
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function palestrantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Palestrante::class,
            'programacao_palestrante',
            'programacao_id',
            'palestrante_id'
        )->withTimestamps();
    }

    // --- ESCOPOS ---
    public function scopeOrdenado($query)
    {
        return $query->orderBy('data_hora_inicio');
    }

    // --- ACESSORS ---
    public function getPeriodoAttribute(): string
    {
        $ini = $this->data_hora_inicio ? $this->data_hora_inicio->format('d/m/Y H:i') : '—';
        $fim = $this->data_hora_fim ? $this->data_hora_fim->format('d/m/Y H:i') : '—';
        return "{$ini} — {$fim}";
    }
}
