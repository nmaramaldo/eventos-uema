<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // só precisamos do evento_id; o controller já checa janela, duplicidade etc.
            'evento_id' => ['required', 'uuid', 'exists:eventos,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'evento_id' => 'evento',
        ];
    }
}
