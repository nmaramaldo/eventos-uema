<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Palestrante extends Model
{
    use HasFactory;

    protected $table = 'palestrantes';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nome',
        'biografia',
        'foto_url',
    ];

    public function eventos()
    {
        return $this->belongsToMany(Event::class, 'evento_palestrante',                  // tabela pivot
        'palestrante_id',
        'evento_id');
    }
}
