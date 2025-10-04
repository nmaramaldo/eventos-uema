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
            'nome'                  => ['required', 'string', 'max:255'],
            'descricao'             => ['nullable', 'string'],

            'tipo_evento'           => ['required', 'string', 'in:presencial,online,hibrido,videoconf'],

            // listas controladas no front; flexível no back
            'tipo_classificacao'    => ['nullable', 'string', 'max:255'],
            'area_tematica'         => ['nullable', 'string', 'max:255'],

            'data_inicio_evento'    => ['required', 'date'],
            'data_fim_evento'       => ['required', 'date', 'after_or_equal:data_inicio_evento'],

            'data_inicio_inscricao' => ['required', 'date'],
            'data_fim_inscricao'    => ['required', 'date', 'after_or_equal:data_inicio_inscricao'],

            'coordenador_id'        => ['nullable', 'uuid', 'exists:users,id'],
            'logomarca_url'         => ['nullable', 'url'],

            // upload de capa (opcional)
            'capa'                  => ['sometimes', 'nullable', 'image', 'max:3072'],

            // opcional
            'status'                => ['nullable', 'in:rascunho,ativo,publicado'],

            // OPCIONAL – vagas
            'vagas'                 => ['sometimes', 'nullable', 'integer', 'min:1'],

            // Passo 4 – Programação
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
            'atividades.*.fim'              => ['nullable', 'date'], // sem after_or_equal (evita falso negativo)
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

            'locais.*.nome'                 => 'nome do local',
            'palestrantes.*.nome'           => 'nome do palestrante',
            'palestrantes.*.email'          => 'e-mail do palestrante',
            'atividades.*.titulo'           => 'título da atividade',
            'atividades.*.inicio'           => 'início da atividade',
            'atividades.*.fim'              => 'fim da atividade',
            'atividades.*.capacidade'       => 'capacidade da atividade',
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
