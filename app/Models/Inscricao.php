<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    use HasFactory;

    protected $table = 'inscricoes';
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
        'presente' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evento()
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function certificado() 
    {
        return $this->hasOne(Certificado::class);
    }
}
