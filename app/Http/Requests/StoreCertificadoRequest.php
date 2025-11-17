<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ajuste se você tiver alguma policy específica
        return true;
    }

    public function rules(): array
    {
        return [
            'inscricao_id'     => ['required', 'exists:inscricoes,id'],
            'modelo_id'        => ['required', 'exists:certificado_modelos,id'],
            'data_emissao'     => ['nullable', 'date'],

            // OPCIONAL ✅
            'url_certificado'  => ['nullable', 'string', 'max:255'],

            // se existirem esses campos na tabela, mantemos opcionais
            'tipo'             => ['nullable', 'string', 'max:50'],
            'hash_verificacao' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'inscricao_id.required' => 'Selecione uma inscrição.',
            'inscricao_id.exists'   => 'Inscrição inválida.',
            'modelo_id.required'    => 'Selecione um modelo de certificado.',
            'modelo_id.exists'      => 'Modelo de certificado inválido.',
            'data_emissao.date'     => 'A data de emissão é inválida.',
        ];
    }
}
