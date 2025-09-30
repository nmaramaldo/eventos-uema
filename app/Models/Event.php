<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    // UUID como chave primária
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'coordenador_id',
        'nome',
        'descricao',

        // NOVOS CAMPOS
        'tipo_classificacao',
        'area_tematica',

        'data_inicio_evento',
        'data_fim_evento',
        'data_inicio_inscricao',
        'data_fim_inscricao',
        'tipo_evento',
        'logomarca_url',
        'status',

        // campo opcional para controle de vagas
        'vagas',
    ];

    protected $casts = [
        'data_inicio_evento'     => 'datetime',
        'data_fim_evento'        => 'datetime',
        'data_inicio_inscricao'  => 'datetime',
        'data_fim_inscricao'     => 'datetime',
        'vagas'                  => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->getKey())) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // ------------------------------------------------------------------
    // Relacionamentos
    // ------------------------------------------------------------------

    /**
     * Não usamos orderBy aqui para evitar erro em colunas que não existem.
     * A ordenação é aplicada nos controllers com ->ordenado().
     */
    public function detalhes(): HasMany
    {
        return $this->hasMany(EventoDetalhe::class, 'evento_id', 'id');
    }

    public function coordenador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordenador_id', 'id');
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'evento_id', 'id');
    }

    public function palestrantes(): BelongsToMany
    {
        return $this
            ->belongsToMany(Palestrante::class, 'evento_palestrante', 'evento_id', 'palestrante_id')
            ->withTimestamps();
    }

    // ------------------------------------------------------------------
    // Helpers / Regras de negócio
    // ------------------------------------------------------------------

    public function isAtivo(): bool
    {
        $status = is_string($this->status) ? strtolower($this->status) : $this->status;
        return in_array($status, ['ativo', 'publicado', 1, true], true);
    }

    public function inscricoesAbertas(): bool
    {
        $now = now();
        return $this->isAtivo()
            && $this->data_inicio_inscricao
            && $this->data_fim_inscricao
            && $this->data_inicio_inscricao <= $now
            && $this->data_fim_inscricao >= $now;
    }

    public function eventoEmAndamento(): bool
    {
        $now = now();
        return $this->data_inicio_evento
            && $this->data_fim_evento
            && $this->data_inicio_evento <= $now
            && $this->data_fim_evento >= $now;
    }

    public function vagasDisponiveis(): ?int
    {
        if (!array_key_exists('vagas', $this->attributes)) {
            return null;
        }
        $total  = (int) $this->getAttribute('vagas');
        $usadas = (int) $this->inscricoes()->count();
        return max(0, $total - $usadas);
    }

    public function getPeriodoEventoAttribute(): string
    {
        $ini = optional($this->data_inicio_evento)->format('d/m/Y H:i');
        $fim = optional($this->data_fim_evento)->format('d/m/Y H:i');
        return trim("{$ini} — {$fim}");
    }

    public function getPeriodoInscricaoAttribute(): string
    {
        $ini = optional($this->data_inicio_inscricao)->format('d/m/Y H:i');
        $fim = optional($this->data_fim_inscricao)->format('d/m/Y H:i');
        return trim("{$ini} — {$fim}");
    }

    // Compat com views antigas
    public function periodo_evento(): string
    {
        return $this->getPeriodoEventoAttribute();
    }
    public function periodo_inscricao(): string
    {
        return $this->getPeriodoInscricaoAttribute();
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeProximos($query)
    {
        return $query
            ->whereNotNull('data_inicio_evento')
            ->where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento', 'asc');
    }
}
