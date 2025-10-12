<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Programacao extends Model
{
    use HasFactory;

    protected $table = 'programacao';

    /**
     * A tabela usa UUID como chave primária.
     * Se a sua tabela for autoincremento inteiro, remova as duas linhas abaixo
     * e o método booted() mais abaixo.
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'evento_id',
        'titulo',
        'descricao',
        'modalidade',
        'data_hora_inicio',
        'data_hora_fim',
        'local_id',     // se existir no schema
        'localidade',   // quando salvar o nome/endereço em vez do id
        'capacidade',
        'requer_inscricao',
    ];

    protected $casts = [
        
        'data_hora_inicio'      => 'datetime',
        'data_hora_fim'         => 'datetime',
        'capacidade'       => 'integer',
        'requer_inscricao' => 'boolean',
    ];

    protected $attributes = [
        'requer_inscricao' => false,
    ];

    /**
     * Gera UUID automaticamente ao criar (quando necessário).
     */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->getKey())) {
                $m->{$m->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /* ============================
     * Relacionamentos
     * ============================ */

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function local(): BelongsTo
    {
        // Só funcionará se houver a coluna local_id na tabela.
        // Se você estiver usando apenas "localidade" (texto), este relacionamento ficará ocioso.
        return $this->belongsTo(Local::class, 'local_id');
    }

    /* ============================
     * Scopes
     * ============================ */

    /**
     * Ordena por data e hora_inicio quando existirem; senão por created_at.
     */
    public function scopeOrdenado($query)
    {
        $table = $this->getTable();

        if (Schema::hasColumn($table, 'data')) {
            $query->orderBy('data', 'asc');

            if (Schema::hasColumn($table, 'hora_inicio')) {
                $query->orderBy('hora_inicio', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'asc');
        }

        return $query;
    }

    /* ============================
     * Normalizações de horário (opcional)
     * ============================ */

    public function setHoraInicioAttribute($value): void
    {
        $this->attributes['hora_inicio'] = $this->normalizeTime($value);
    }

    public function setHoraFimAttribute($value): void
    {
        $this->attributes['hora_fim'] = $this->normalizeTime($value);
    }

    private function normalizeTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // aceita "8", "08", "8:00", "08:00", "08:00:00" etc.
        $value = trim((string) $value);

        // se só vier "HH" (ex.: "8"), vira "08:00:00"
        if (preg_match('/^\d{1,2}$/', $value)) {
            return str_pad($value, 2, '0', STR_PAD_LEFT) . ':00:00';
        }

        // se vier "HH:MM"
        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            [$h, $m] = explode(':', $value);
            $h = str_pad($h, 2, '0', STR_PAD_LEFT);
            return "{$h}:{$m}:00";
        }

        // se vier "HH:MM:SS" já retorna
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $value)) {
            [$h, $m, $s] = explode(':', $value);
            $h = str_pad($h, 2, '0', STR_PAD_LEFT);
            return "{$h}:{$m}:{$s}";
        }

        // fallback: tenta extrair HH e MM
        if (preg_match('/(?P<h>\d{1,2})[:hH\.](?P<m>\d{2})/', $value, $m)) {
            $h = str_pad($m['h'], 2, '0', STR_PAD_LEFT);
            $mm = $m['m'];
            return "{$h}:{$mm}:00";
        }

        return null;
    }
}
