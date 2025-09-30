<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // IDs como UUID (string)
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Gera UUID e define defaults ao criar.
     */
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->getKey())) {
                $user->{$user->getKeyName()} = (string) Str::uuid();
            }

            // Defaults seguros para cadastro
            if ($user->tipo_usuario === null) {
                $user->tipo_usuario = 'comum';
            }

            if ($user->ativo === null) {
                $user->ativo = true;
            }
        });
    }

    /**
     * Campos liberados para mass assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario', // 'comum' | 'admin' | 'master' (ou enum)
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
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'ativo'             => 'boolean',
            'tipo_usuario'      => UserType::class, // enum de apoio
        ];
    }

    // -----------------------
    // Helpers de papel/permissão
    // -----------------------

    /**
     * Retorna o valor string do tipo do usuário,
     * independente de estar usando enum ou string.
     */
    public function tipoUsuarioValue(): ?string
    {
        $t = $this->tipo_usuario;

        if ($t instanceof \BackedEnum) {
            return $t->value;
        }

        return $t !== null ? (string) $t : null;
    }

    public function isAdmin(): bool
    {
        return in_array($this->tipoUsuarioValue(), ['admin', 'master'], true);
    }

    public function isMaster(): bool
    {
        return $this->tipoUsuarioValue() === 'master';
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
