<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    // PK com UUID (tabela não autoincrementa)
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'nome',
        'tipo',
        'campus',
        'predio',
        'sala',
        'capacidade',
        'observacoes',
        // 'evento_id', // só se existir na tabela
    ];

    protected $casts = [
        'capacidade' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
