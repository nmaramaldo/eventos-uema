<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CertificadoModelo extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'certificado_modelos';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'evento_id',
        'titulo',
        'slug_tipo',        // ex.: participacao, organizacao, palestrante
        'atribuicao',       // ex.: "Todos os inscritos", "Apenas presentes"
        'publicado',        // bool
        'corpo_html',       // texto com {tags}
        'background_path',  // caminho da imagem de fundo (opcional)
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'modelo_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes úteis
    |--------------------------------------------------------------------------
    */

    public function scopeDoEvento($query, string $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }
}
