<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// RELACIONADOS
use App\Models\Programacao;
use App\Models\User;
use App\Models\Inscricao;
use App\Models\Palestrante;

class Event extends Model
{
    use Auditable;
    use HasFactory;
    use HasUuids; // UUID automático (dispensa $incrementing/$keyType/booted)

    protected $table = 'eventos';

    protected $fillable = [
        'coordenador_id',
        'nome',
        'descricao',
        'tipo_classificacao',
        'area_tematica',
        'data_inicio_evento',
        'data_fim_evento',
        'data_inicio_inscricao',
        'data_fim_inscricao',
        'tipo_evento',
        'logomarca_path',   // nome de coluna correto
        'status',
        'vagas',
    ];

    protected $casts = [
        'data_inicio_evento'    => 'datetime',
        'data_fim_evento'       => 'datetime',
        'data_inicio_inscricao' => 'datetime',
        'data_fim_inscricao'    => 'datetime',
        'vagas'                 => 'integer',
    ];

    /* =========================
     * Relacionamentos
     * ========================= */

    // Programação oficial
    public function programacao(): HasMany
    {
        return $this->hasMany(Programacao::class, 'evento_id', 'id');
    }

    // ✅ Alias para compatibilidade com views antigas que usam $evento->detalhes
    public function detalhes(): HasMany
    {
        return $this->hasMany(Programacao::class, 'evento_id', 'id');
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

    /* =========================
     * Acessors / Helpers
     * ========================= */

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

    public function inscricoesAbertas(): bool
    {
        $now = now();

        return $this->isAtivo()
            && $this->data_inicio_inscricao && $this->data_inicio_inscricao <= $now
            && $this->data_fim_inscricao && $this->data_fim_inscricao >= $now;
    }

    public function vagasDisponiveis(): ?int
    {
        if (empty($this->vagas)) {
            // nulo/0 -> ilimitado
            return null;
        }

        $inscritos = $this->inscricoes()->count();

        return max(0, $this->vagas - $inscritos);
    }

    public function isAtivo(): bool
    {
        $status = is_string($this->status) ? strtolower($this->status) : $this->status;
        return in_array($status, ['ativo', 'publicado'], true);
    }

    // Compat para chamadas antigas em views
    public function periodo_evento(): string
    {
        return $this->getPeriodoEventoAttribute();
    }
    public function periodo_inscricao(): string
    {
        return $this->getPeriodoInscricaoAttribute();
    }

    /* =========================
     * Scopes
     * ========================= */
    public function scopeProximos($query)
    {
        return $query
            ->whereNotNull('data_inicio_evento')
            ->where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento', 'asc');
    }
}
