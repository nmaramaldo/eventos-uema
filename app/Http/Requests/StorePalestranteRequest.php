<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePalestranteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'palestrantes' => ['required', 'array', 'min:1'],
            'palestrantes.*.nome' => ['required', 'string', 'max:255'],
            'palestrantes.*.email' => ['nullable', 'email', 'max:255'],
            'palestrantes.*.biografia' => ['nullable', 'string'],
            'foto_url' => 'nullable|string',
            'eventos' => 'nullable|array',
            'eventos.*' => 'exists:eventos,id'
        ];
    }

    public function messages()
    {
        return [
            'palestrantes.required' => 'Adicione pelo menos um palestrante.',
            'palestrantes.*.nome.required' => 'O campo nome é obrigatório para todos os palestrantes.',
            'palestrantes.*.email.email' => 'O campo email deve ser válido.',
        ];
    }
}
