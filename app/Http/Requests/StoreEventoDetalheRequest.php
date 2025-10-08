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
            'evento_id' => 'required|exists:eventos,id',
            'descricao' => 'required|string',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required|after:hora_inicio',
            'modalidade' => 'required|string|max:100',
            'capacidade' => 'nullable|integer',
            'localidade' => 'required|string|max:255'
        ];
    }
}
