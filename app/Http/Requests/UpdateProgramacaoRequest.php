<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evento_id'        => 'sometimes|exists:eventos,id',
            'titulo'           => 'sometimes|string|max:255',
            'descricao'        => 'sometimes|string',
            'data'             => 'sometimes|date',
            'hora_inicio'      => 'sometimes',
            'hora_fim'         => 'sometimes|after:hora_inicio',
            'modalidade'       => 'sometimes|string|max:100',
            'capacidade'       => 'sometimes|nullable|integer',
            'localidade'       => 'required|string|max:255',

            // 💡 nova parte: vínculo de palestrantes é opcional, mas validado
            'palestrantes'     => 'sometimes|array',
            'palestrantes.*'   => 'distinct|exists:palestrantes,id',
        ];
    }
}
