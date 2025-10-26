<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'                   => 'required|string|max:255',
            'descricao'              => 'required|string',
            'tipo_classificacao'     => 'required|string|max:255',
            'area_tematica'          => 'required|string|max:255',
            'data_inicio_evento'     => 'required|date',
            'data_fim_evento'        => 'required|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao'  => 'required|date',
            'data_fim_inscricao'     => 'required|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento'            => 'required|string|max:50',
            'logomarca'              => 'nullable|image|mimes:jpeg,png,gif|max:5120',
            'status'                 => 'required|string|max:50',
            'vagas'                  => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'                  => 'nome do evento',
            'descricao'             => 'descrição',
            'tipo_evento'           => 'tipo do evento',
            'tipo_classificacao'    => 'categoria do evento',
            'area_tematica'         => 'área temática',
            'data_inicio_evento'    => 'início do evento',
            'data_fim_evento'       => 'término do evento',
            'data_inicio_inscricao' => 'início das inscrições',
            'data_fim_inscricao'    => 'término das inscrições',
            'logomarca'         => 'URL da logomarca',
            'status'                => 'status',
            'vagas'                 => 'vagas do evento',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_evento.in'                    => 'Selecione um tipo de evento válido.',
            'data_fim_evento.after_or_equal'    => 'O término do evento deve ser igual ou posterior ao início.',
            'data_fim_inscricao.after_or_equal' => 'O término das inscrições deve ser igual ou posterior ao início.',
        ];
    }
}
