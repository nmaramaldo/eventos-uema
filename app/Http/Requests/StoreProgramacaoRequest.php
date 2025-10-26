<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramacaoRequest extends FormRequest
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
            'atividades' => 'required|array|min:1',
            'atividades.*.titulo' => 'required|string|max:255',
            'atividades.*.descricao' => 'nullable|string',
            'atividades.*.data_hora_inicio' => 'required|date',
            'atividades.*.data_hora_fim' => 'required|date|after_or_equal:atividades.*.data_hora_inicio',
            'atividades.*.modalidade' => 'required|string|max:255',
            'atividades.*.capacidade' => 'nullable|integer|min:1',
            'atividades.*.localidade' => 'required|string|max:255',
            'atividades.*.requer_inscricao' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'atividades.required' => 'É necessário adicionar pelo menos uma atividade',
            'atividades.*.titulo.required' => 'O titulo da atividade é obrigatório.',
            'atividades.*.data_hora_inicio.required' => 'A data/hora de início é obrigatória.',
            'atividades.*.data_hora_fim.after_or_equal' => 'O término deve ser após o início.',
            'atividades.*.modalidade.required' => 'A modalidade é obrigatória.',
            'atividades.*.localidade.required' => 'A localidade é obrigatória.',
        ];
    }

    public function attributes(): array
    {
        return [
            'atividades.*.titulo' => 'título',
            'atividades.*.data_hora_inicio' => 'início',
            'atividades.*.data_hora_fim' => 'término',
            'atividades.*.localidade' => 'local',
        ];
    }
}
