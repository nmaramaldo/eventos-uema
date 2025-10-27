<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Programacao extends Model
{
    use HasFactory;

    protected $table = 'programacao';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'evento_id',
        'titulo',
        'descricao',
        'modalidade',

        'data', 'hora_inicio', 'hora_fim',
        'inicio_em', 'termino_em',
        'data_hora_inicio', 'data_hora_fim',

        'local_id',
        'localidade',
        'capacidade',
        'requer_inscricao',
    ];

    protected $casts = [
        'capacidade'       => 'integer',
        'requer_inscricao' => 'boolean',
    ];

    protected $attributes = [
        'requer_inscricao' => false,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    /** Palestrantes desta atividade */
    public function palestrantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Palestrante::class,
            'programacao_palestrante',
            'programacao_id',
            'palestrante_id'
        )->withTimestamps();
    }

    public function scopeOrdenado($query)
    {
        $t = $this->getTable();

        if (Schema::hasColumn($t, 'data')) {
            $query->orderBy('data')
                  ->when(Schema::hasColumn($t, 'hora_inicio'), fn ($q) => $q->orderBy('hora_inicio'));
        } elseif (Schema::hasColumn($t, 'inicio_em')) {
            $query->orderBy('inicio_em');
        } elseif (Schema::hasColumn($t, 'data_hora_inicio')) {
            $query->orderBy('data_hora_inicio');
        } else {
            $query->orderBy('created_at');
        }

        return $query;
    }

    public function getInicioCarbonAttribute(): ?Carbon
    {
        if (!empty($this->data)) {
            $hi = $this->hora_inicio ? substr((string) $this->hora_inicio, 0, 8) : '00:00:00';
            return Carbon::parse($this->data . ' ' . $hi);
        }

        $campo = $this->inicio_em ?? $this->data_hora_inicio ?? null;
        return $campo ? Carbon::parse($campo) : null;
    }

    public function getFimCarbonAttribute(): ?Carbon
    {
        if (!empty($this->data)) {
            $hf = $this->hora_fim ? substr((string) $this->hora_fim, 0, 8) : '00:00:00';
            return Carbon::parse($this->data . ' ' . $hf);
        }

        $campo = $this->termino_em ?? $this->data_hora_fim ?? null;
        return $campo ? Carbon::parse($campo) : null;
    }

    public function getPeriodoAttribute(): string
    {
        $ini = $this->inicio_carbon ? $this->inicio_carbon->format('d/m/Y H:i') : '—';
        $fim = $this->fim_carbon    ? $this->fim_carbon->format('d/m/Y H:i')    : '—';
        return "{$ini} — {$fim}";
    }

    public function setHoraInicioAttribute($value): void
    {
        $this->attributes['hora_inicio'] = $this->normalizeTime($value);
    }

    public function setHoraFimAttribute($value): void
    {
        $this->attributes['hora_fim'] = $this->normalizeTime($value);
    }

    private function normalizeTime($value): ?string
    {
        if ($value === null || $value === '') return null;
        $value = trim((string) $value);

        if (preg_match('/^\d{1,2}$/', $value)) {
            return str_pad($value, 2, '0', STR_PAD_LEFT) . ':00:00';
        }
        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            [$h, $m] = explode(':', $value);
            $h = str_pad($h, 2, '0', STR_PAD_LEFT);
            return "{$h}:{$m}:00";
        }
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $value)) {
            [$h, $m, $s] = explode(':', $value);
            $h = str_pad($h, 2, '0', STR_PAD_LEFT);
            return "{$h}:{$m}:{$s}";
        }
        if (preg_match('/(?P<h>\d{1,2})[:hH\.](?P<m>\d{2})/', $value, $m)) {
            $h  = str_pad($m['h'], 2, '0', STR_PAD_LEFT);
            $mm = $m['m'];
            return "{$h}:{$mm}:00";
        }
        return null;
    }
}
