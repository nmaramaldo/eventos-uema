<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Palestrante extends Model
{
    use HasFactory;

    protected $table = 'palestrantes';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'nome',
        'email',
        'biografia',
        'foto',     // << necessário pro upload
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function eventos()
    {
        return $this->belongsToMany(
            Event::class,
            'evento_palestrante',
            'palestrante_id',
            'evento_id'
        )->withTimestamps();
    }

    /** Atividades (programação) em que este palestrante participa */
    public function atividades()
    {
        return $this->belongsToMany(
            Programacao::class,
            'programacao_palestrante',
            'palestrante_id',
            'programacao_id'
        )->withTimestamps();
    }

    /** URL pública da foto (ou null) */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? \Storage::disk('public')->url($this->foto) : null;
    }
}
