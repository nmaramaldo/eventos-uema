<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inscricao_id'     => ['sometimes', 'required', 'exists:inscricoes,id'],
            'modelo_id'        => ['sometimes', 'required', 'exists:certificado_modelos,id'],
            'data_emissao'     => ['nullable', 'date'],

            // OPCIONAL AQUI TAMBÉM ✅
            'url_certificado'  => ['nullable', 'string', 'max:255'],

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
