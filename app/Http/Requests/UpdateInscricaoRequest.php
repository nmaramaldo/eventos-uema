<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscricaoRequest extends FormRequest
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
            'user_id' => 'sometimes|uuid|exists:users,id',
            'evento_id' => 'sometimes|uuid|exists:eventos,id',
            'status' => 'sometimes|string|in:pendente,aprovado,cancelado',
            'data_inscricao' => 'sometimes|date',
            'presente' => 'sometimes|boolean',
        ];
    }
}
