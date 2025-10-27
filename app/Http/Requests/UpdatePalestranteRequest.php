<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePalestranteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'      => 'sometimes|string|max:255',
            'biografia' => 'sometimes|string',
            'foto_url'  => 'sometimes|string',
            'eventos'   => 'sometimes|array',
            'eventos.*' => 'exists:eventos,id',

            // novas
            'atividades'   => 'sometimes|array',
            'atividades.*' => 'exists:programacao,id',
        ];
    }
}
