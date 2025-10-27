<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePalestranteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'palestrantes'                 => ['required', 'array', 'min:1'],
            'palestrantes.*.nome'         => ['required', 'string', 'max:255'],
            'palestrantes.*.email'        => ['nullable', 'email', 'max:255'],
            'palestrantes.*.biografia'    => ['nullable', 'string'],
            'palestrantes.*.foto'         => ['nullable', 'image', 'max:2048'],
            'palestrantes.*.atividades'   => ['nullable', 'array'],
            'palestrantes.*.atividades.*' => ['string'], // UUID
        ];
    }

    public function messages()
    {
        return [
            'palestrantes.required'      => 'Adicione pelo menos um palestrante.',
            'palestrantes.*.nome.required' => 'O campo nome é obrigatório para todos os palestrantes.',
            'palestrantes.*.email.email' => 'O campo email deve ser válido.',
        ];
    }
}
