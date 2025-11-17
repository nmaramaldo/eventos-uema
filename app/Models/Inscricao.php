<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Inscricao extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'inscricoes';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'evento_id',
        'status',
        'data_inscricao',
        'presente',
    ];

    protected $casts = [
        'data_inscricao' => 'datetime',
        'presente'       => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
            if (empty($m->data_inscricao)) {
                $m->data_inscricao = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias para compatibilidade com o controller/view
    public function usuario(): BelongsTo
    {
        return $this->user();
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(Certificado::class);
    }

    public function scopeDoUsuario($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function participante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers para geração de certificado
    |--------------------------------------------------------------------------
    */

    /**
     * Dados que podem ser usados nas TAGS do certificado.
     * Exemplo de chaves:
     *  - nome_participante
     *  - email_participante
     *  - nome_evento
     *  - data_inicio_evento
     *  - data_termino_evento
     *  - carga_horaria (se o evento tiver esse campo)
     */
    public function dadosParaCertificado(): array
    {
        $user   = $this->user ?? $this->usuario;
        $evento = $this->evento;

        return [
            'nome_participante'   => $user?->name ?? '',
            'email_participante'  => $user?->email ?? '',
            'nome_evento'         => $evento?->nome ?? '',
            'data_inicio_evento'  => optional($evento?->data_inicio_evento)->format('d/m/Y'),
            'data_termino_evento' => optional($evento?->data_fim_evento)->format('d/m/Y'),
            // ajuste se você criar um campo de carga horária no evento
            'carga_horaria'       => $evento->carga_horaria ?? '',
        ];
    }

    /**
     * Faz a substituição das TAGS no corpo do modelo.
     * Ex.: "Certificamos que {nome_participante} participou do evento {nome_evento}"
     */
    public function preencherModeloCertificado(string $corpo): string
    {
        $dados = $this->dadosParaCertificado();

        $search  = [];
        $replace = [];

        foreach ($dados as $chave => $valor) {
            $search[]  = '{' . $chave . '}';
            $replace[] = $valor;
        }

        return str_replace($search, $replace, $corpo);
    }
}
