<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Inscricao extends Model
{
    use HasFactory;

    protected $table = 'inscricoes';

    // PK como UUID
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

    /**
     * Define automaticamente o UUID e a data de inscrição (se não informada).
     */
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

    // -------------------
    // Relacionamentos
    // -------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(Certificado::class);
    }

    // -------------------
    // Scopes auxiliares
    // -------------------

    public function scopeDoUsuario($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }
}
