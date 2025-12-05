<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';

    // vamos usar UUID como chave primária
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'inscricao_id',
        'modelo_id',
        'tipo',             // participante / palestrante / organizador
        'path',
        'data_emissao',
        'hash_verificacao',
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
    ];

    /**
     * Gera automaticamente:
     * - id (uuid)
     * - data_emissao (se vazio)
     * - hash_verificacao (para validação do certificado)
     */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }

            if (empty($m->data_emissao)) {
                $m->data_emissao = now();
            }

            if (empty($m->hash_verificacao)) {
                $m->hash_verificacao = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(CertificadoModelo::class, 'modelo_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: texto com tags substituídas
    |--------------------------------------------------------------------------
    */

    /**
     * Texto final do certificado, com as tags {nome_participante}, {nome_evento}, etc.
     * substituídas pelos dados reais.
     */
    public function getTextoRenderizadoAttribute(): string
    {
        $modelo    = $this->modelo;
        $inscricao = $this->inscricao;
        $user      = $inscricao?->user ?? $inscricao?->usuario;
        $evento    = $inscricao?->evento;

        // se faltar alguma coisa essencial, devolve o HTML cru do modelo
        if (!$modelo || !$inscricao || !$user || !$evento) {
            return $modelo?->corpo_html ?? '';
        }

        $search = [
            '{nome_participante}',
            '{nome_evento}',
            '{data_inicio_evento}',
            '{data_fim_evento}',
            '{carga_horaria}',
            '{nome_organizador}',
            '{nome_palestrante}',
            '{local_evento}',
        ];

        $replace = [
            $user->name,
            $evento->nome,
            optional($evento->data_inicio_evento)->format('d/m/Y'),
            optional($evento->data_fim_evento)->format('d/m/Y'),
            $evento->carga_horaria ?? ($modelo->carga_horaria_padrao ?? ''),
            $evento->organizador ?? '',
            $evento->palestrante_principal ?? '',
            $evento->local ?? '',
        ];

        return str_replace($search, $replace, $modelo->corpo_html ?? '');
    }
}
