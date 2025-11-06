<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAjaxProgramacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza campos vindos do front (datas pt-BR, local id/nome, aliases).
     */
    protected function prepareForValidation(): void
    {
        $inicio = $this->input('data_hora_inicio', $this->input('inicio'));
        $fim    = $this->input('data_hora_fim',    $this->input('fim'));

        $parseDate = function ($value) {
            if (!$value) return null;
            $value = trim((string)$value);
            $formats = [
                'd/m/Y H:i', 'd/m/Y H:i:s',
                'Y-m-d H:i', 'Y-m-d H:i:s',
                'Y-m-d\TH:i', 'Y-m-d\TH:i:s',
            ];
            foreach ($formats as $fmt) {
                $dt = \DateTime::createFromFormat($fmt, $value);
                if ($dt !== false) {
                    return $dt->format('Y-m-d H:i:s');
                }
            }
            return $value; // deixa a validação acusar se ficar inválido
        };

        // Decide automaticamente se o "local" recebido é id numérico ou nome
        $rawLocal   = $this->input('local_id', $this->input('local', $this->input('localidade')));
        $localId    = null;
        $localNome  = null;
        if ($rawLocal !== null && $rawLocal !== '') {
            if (is_numeric($rawLocal)) $localId = (int) $rawLocal;
            else                       $localNome = trim((string)$rawLocal);
        }

        $this->merge([
            'data_hora_inicio' => $parseDate($inicio),
            'data_hora_fim'    => $parseDate($fim),
            'capacidade'       => $this->input('capacidade', $this->input('vagas')),
            'local_id'         => $localId,
            'localidade'       => $localNome,
            'requer_inscricao' => $this->boolean('requer_inscricao'),
        ]);
    }

    public function rules(): array
    {
        return [
            'titulo'            => 'required|string|max:255',
            'descricao'         => 'nullable|string',
            'data_hora_inicio'  => 'required|date',
            'data_hora_fim'     => 'required|date|after_or_equal:data_hora_inicio',
            'modalidade'        => 'nullable|string|max:255',
            'capacidade'        => 'nullable|integer|min:1',
            // Pelo menos um: local_id OU localidade (nome)
            'local_id'          => 'nullable|integer|exists:locais,id|required_without:localidade',
            'localidade'        => 'nullable|string|max:255|required_without:local_id',
            'requer_inscricao'  => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'              => 'O título da atividade é obrigatório.',
            'data_hora_inicio.required'    => 'A data/hora de início é obrigatória.',
            'data_hora_fim.after_or_equal' => 'O término deve ser após (ou igual a) o início.',
            'local_id.required_without'    => 'Informe um local existente ou preencha o nome do local.',
            'localidade.required_without'  => 'Informe o nome do local ou selecione um local existente.',
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo'           => 'título',
            'data_hora_inicio' => 'início',
            'data_hora_fim'    => 'término',
            'localidade'       => 'local',
            'local_id'         => 'local',
            'capacidade'       => 'vagas',
        ];
    }

    /**
     * Garante JSON quando a validação falhar (evita HTML/redirect).
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
