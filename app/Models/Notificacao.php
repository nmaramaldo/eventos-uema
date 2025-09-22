<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensagem',
        'enviado_em',
        'lido',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
        'lido' => 'boolean',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
    
}
