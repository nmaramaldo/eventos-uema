<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAjaxProgramacaoRequest extends FormRequest
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
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'required|date|after_or_equal:data_hora_inicio',
            'modalidade' => 'required|string|max:255',
            'capacidade' => 'nullable|integer|min:1',
            'localidade' => 'required|string|max:255',
            'requer_inscricao' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'O titulo da atividade é obrigatório.',
            'data_hora_inicio.required' => 'A data/hora de início é obrigatória.',
            'data_hora_fim.after_or_equal' => 'O término deve ser após o início.',
            'modalidade.required' => 'A modalidade é obrigatória.',
            'localidade.required' => 'A localidade é obrigatória.',
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'data_hora_inicio' => 'início',
            'data_hora_fim' => 'término',
            'localidade' => 'local',
        ];
    }
}
