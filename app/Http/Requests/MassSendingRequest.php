<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassSendingRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:3',
            'message' => 'nullable|string|max:4096',
            'media_caption' => 'nullable|string|max:1000',
            'media_type' => 'nullable|string|in:text,image,video,audio,document',
            'media_data' => 'nullable|string',
            'wuzapi_participants' => 'nullable|array|min:1',
            'wuzapi_participants.*' => 'string|max:50',
            'manual_numbers' => 'nullable|string|max:10000',
            'scheduled_at' => 'nullable|date|after:now',
            'group_id' => 'nullable|exists:groups,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da campanha é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'message.max' => 'A mensagem não pode ter mais de 4096 caracteres.',
            'media_caption.max' => 'A legenda da mídia não pode ter mais de 1000 caracteres.',
            'media_type.in' => 'Tipo de mídia inválido.',
            'wuzapi_participants.min' => 'Selecione pelo menos um grupo ou adicione números manualmente.',
            'wuzapi_participants.*.max' => 'Cada participante deve ter no máximo 50 caracteres.',
            'manual_numbers.max' => 'Os números manuais não podem exceder 10000 caracteres.',
            'scheduled_at.after' => 'A data agendada deve ser no futuro.',
            'group_id.exists' => 'O grupo selecionado não existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome da campanha',
            'message' => 'mensagem',
            'media_caption' => 'legenda da mídia',
            'media_type' => 'tipo de mídia',
            'media_data' => 'dados da mídia',
            'wuzapi_participants' => 'participantes',
            'manual_numbers' => 'números manuais',
            'scheduled_at' => 'data agendada',
            'group_id' => 'grupo',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        $this->merge([
            'name' => strip_tags(trim($this->name ?? '')),
            'message' => strip_tags(trim($this->message ?? '')),
            'media_caption' => strip_tags(trim($this->media_caption ?? '')),
            'manual_numbers' => trim($this->manual_numbers ?? ''),
        ]);
    }
}
