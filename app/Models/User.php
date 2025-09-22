<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Enums\UserType;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // IDs string (UUID)
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Preenche automaticamente o UUID ao criar.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->getKey())) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Campos liberados para mass-assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario', // 'comum' | 'admin' | 'master'
        'ativo',        // bool
    ];

    /**
     * Campos ocultos na serialização.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts (modo novo).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'ativo'             => 'boolean',
            'tipo_usuario' => UserType::class,
        ];
    }

    // -----------------------
    // Conveniências de papel
    // -----------------------
    public function isAdmin(): bool
    {
        return in_array($this->tipo_usuario, ['admin', 'master'], true);
    }

    public function isMaster(): bool
    {
        return $this->tipo_usuario === 'master';
    }

    // -----------------------
    // Relacionamentos
    // -----------------------
    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'user_id');
    }

    public function eventosCoordenados(): HasMany
    {
        return $this->hasMany(Event::class, 'coordenador_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class);
    }
}
