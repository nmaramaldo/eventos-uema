<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';

    protected $fillable = [
        'inscricao_id',
        'url_certificado',
        'data_emissao',
    ];

    protected $casts = [
        'data_emissao' => 'date'
    ];


    public function inscricao()
    {
        return $this->belongsTo(Inscricao::class);
    }
}
