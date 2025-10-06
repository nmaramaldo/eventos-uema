<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'                  => ['sometimes', 'string', 'max:255'],
            'descricao'             => ['sometimes', 'nullable', 'string'],

            'tipo_evento'           => ['sometimes', 'string', 'in:presencial,online,hibrido,videoconf'],

            'tipo_classificacao'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'area_tematica'         => ['sometimes', 'nullable', 'string', 'max:255'],

            'data_inicio_evento'    => ['sometimes', 'date'],
            'data_fim_evento'       => ['sometimes', 'date', 'after_or_equal:data_inicio_evento'],

            'data_inicio_inscricao' => ['sometimes', 'date'],
            'data_fim_inscricao'    => ['sometimes', 'date', 'after_or_equal:data_inicio_inscricao'],

            'coordenador_id'        => ['sometimes', 'nullable', 'uuid', 'exists:users,id'],
            'logomarca_url'         => ['sometimes', 'nullable', 'url'],

            // upload de capa (opcional)
            'capa'                  => ['sometimes', 'nullable', 'image', 'max:3072'],

            'status'                => ['sometimes', 'nullable', 'in:rascunho,ativo,publicado'],
            'vagas'                 => ['sometimes', 'nullable', 'integer', 'min:1'],

            // Passo 4 – tudo opcional
            'locais'                        => ['sometimes', 'array'],
            'locais.*.nome'                 => ['required_with:locais', 'string', 'max:255'],

            'palestrantes'                  => ['sometimes', 'array'],
            'palestrantes.*.nome'           => ['required_with:palestrantes', 'string', 'max:255'],
            'palestrantes.*.email'          => ['nullable', 'email'],
            'palestrantes.*.cargo'          => ['nullable', 'string', 'max:255'],
            'palestrantes.*.mini_bio'       => ['nullable', 'string'],
            'palestrantes.*.foto_url'       => ['nullable', 'url'],

            'atividades'                    => ['sometimes', 'array'],
            'atividades.*.titulo'           => ['required_with:atividades', 'string', 'max:255'],
            'atividades.*.tipo'             => ['nullable', 'string', 'max:100'],
            'atividades.*.inicio'           => ['nullable', 'date'],
            'atividades.*.fim'              => ['nullable', 'date'], // sem after_or_equal
            'atividades.*.local_key'        => ['nullable', 'string', 'max:255'],
            'atividades.*.capacidade'       => ['nullable', 'integer', 'min:1'],
            'atividades.*.requer_inscricao' => ['nullable', 'boolean'],
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
            'coordenador_id'        => 'coordenador',
            'logomarca_url'         => 'URL da logomarca',
            'capa'                  => 'imagem de capa',
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
