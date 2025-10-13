<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// ✅ CORREÇÃO 2: Importa o trait de UUIDs
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Event extends Model
{
    use Auditable;
    use HasFactory;
    // ✅ CORREÇÃO 2: Adiciona o trait para gerar UUIDs automaticamente
    use HasUuids;

    protected $table = 'eventos';

    // ❌ CORREÇÃO 2: As 3 linhas abaixo não são mais necessárias com o trait HasUuids
    // public $incrementing = false;
    // protected $keyType   = 'string';

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
        // ✅ CORREÇÃO 3: Usando o nome correto da coluna
        'logomarca_path',
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

    // ❌ CORREÇÃO 2: O método booted() para gerar UUID não é mais necessário
    // protected static function booted(): void { ... }

    /**
     * ✅ CORREÇÃO 1: Renomeado de 'detalhes' para 'programacao'.
     * Isso resolve o erro 'Call to undefined method programacao()'.
     */
    public function programacao(): HasMany
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
      
        return $this->isAtivo() &&
               $this->data_inicio_inscricao && $this->data_inicio_inscricao <= $now &&
               $this->data_fim_inscricao && $this->data_fim_inscricao >= $now;
    }

     public function vagasDisponiveis(): ?int
    {
        // Se a coluna 'vagas' for nula ou zero, consideramos que as vagas são ilimitadas.
        if (empty($this->vagas)) {
            return null; // Retorna nulo para representar "ilimitado"
        }

        // Conta o número de inscrições já confirmadas para este evento.
        $inscritos = $this->inscricoes()->count();

        // Calcula a diferença e garante que o resultado nunca seja negativo.
        return max(0, $this->vagas - $inscritos);
    }

    public function isAtivo(): bool
    {
        // Retorna true se o status for 'ativo' ou 'publicado'
        return in_array($this->status, ['ativo', 'publicado']);
    }
   
    public function periodo_evento(): string
    {
        return $this->getPeriodoEventoAttribute();
    }
    public function periodo_inscricao(): string
    {
        return $this->getPeriodoInscricaoAttribute();
    }

    public function scopeProximos($query)
    {
        return $query
            ->whereNotNull('data_inicio_evento')
            ->where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento', 'asc');
    }
}
