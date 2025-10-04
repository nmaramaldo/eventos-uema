<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    protected $fillable = [
        'nome',
        'tipo',
        'campus',
        'predio',
        'sala',
        'capacidade',
        'observacoes',
        // 'evento_id', // descomente se essa coluna existir na tabela
    ];

    protected $casts = [
        'capacidade' => 'integer',
    ];
}
