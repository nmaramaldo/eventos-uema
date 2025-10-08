<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Programacao extends Model
{
    use HasFactory;

    protected $table = 'programacao';

    // PK é UUID (string)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'evento_id',
        'descricao',
        'data',          // date (YYYY-MM-DD)
        'hora_inicio',   // time (HH:MM[:SS])
        'hora_fim',      // time (HH:MM[:SS])
        'modalidade',
        'capacidade',
        'localidade',
    ];

    protected $casts = [
        'data'       => 'date',
        'capacidade' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** Compat: view usa $d->titulo; mapeia para 'descricao' */
    public function getTituloAttribute(): ?string
    {
        return $this->attributes['titulo'] ?? ($this->attributes['descricao'] ?? null);
    }

    /** data + hora_inicio -> Carbon */
    public function getDataHoraInicioAttribute(): ?Carbon
    {
        if (!$this->data || empty($this->attributes['hora_inicio'] ?? null)) {
            return null;
        }
        $hi = substr((string) $this->attributes['hora_inicio'], 0, 8);
        return Carbon::parse($this->data->format('Y-m-d') . ' ' . $hi);
    }

    /** data + hora_fim -> Carbon */
    public function getDataHoraFimAttribute(): ?Carbon
    {
        if (!$this->data || empty($this->attributes['hora_fim'] ?? null)) {
            return null;
        }
        $hf = substr((string) $this->attributes['hora_fim'], 0, 8);
        return Carbon::parse($this->data->format('Y-m-d') . ' ' . $hf);
    }

    /** Período formatado */
    public function getPeriodoAttribute(): string
    {
        $d  = $this->data ? $this->data->format('d/m/Y') : '—';
        $hi = !empty($this->attributes['hora_inicio']) ? substr((string) $this->attributes['hora_inicio'], 0, 5) : '—';
        $hf = !empty($this->attributes['hora_fim'])    ? substr((string) $this->attributes['hora_fim'], 0, 5)    : '—';
        return $d . ' • ' . $hi . '–' . $hf;
    }

    // ----------------- Relacionamentos -----------------

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id', 'id');
    }

    public function palestrantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Palestrante::class,
            'evento_detalhe_palestrante',
            'evento_detalhe_id',
            'palestrante_id'
        )->withTimestamps();
    }

    // ----------------- Scopes -----------------

    public function scopeDoEvento($query, string $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }

    public function scopeOrdenado($query)
    {
        return $query
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->orderBy('id');
    }
}
