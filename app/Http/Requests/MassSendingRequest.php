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
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $mediaType = $this->input('media_type');
            $mediaData = $this->input('media_data');
            $message = $this->input('message');

            // Se é uma campanha de mídia, validar media_data
            if (in_array($mediaType, ['image', 'video', 'audio', 'document'])) {
                if (empty($mediaData)) {
                    $validator->errors()->add('media_data', 'Dados da mídia são obrigatórios para campanhas de mídia.');
                    return;
                }

                // Decodificar e validar estrutura do media_data
                $decodedMediaData = is_string($mediaData) ? json_decode($mediaData, true) : $mediaData;
                
                if (!$decodedMediaData || !is_array($decodedMediaData)) {
                    $validator->errors()->add('media_data', 'Dados da mídia devem ser um JSON válido.');
                    return;
                }

                // Validar campos obrigatórios do media_data
                if (empty($decodedMediaData['base64'])) {
                    $validator->errors()->add('media_data', 'Dados base64 da mídia são obrigatórios.');
                    return;
                }

                // Validar tamanho do base64 (máximo 10MB)
                $base64Length = strlen($decodedMediaData['base64']);
                $maxSize = 10 * 1024 * 1024; // 10MB
                if ($base64Length > $maxSize) {
                    $validator->errors()->add('media_data', 'Arquivo muito grande. Tamanho máximo permitido: 10MB.');
                    return;
                }

                // Validar se base64 é válido
                if (!preg_match('/^data:[^;]+;base64,/', $decodedMediaData['base64'])) {
                    $validator->errors()->add('media_data', 'Formato base64 inválido. Deve incluir o prefixo data:type;base64,');
                    return;
                }

                // Para documentos, validar nome do arquivo
                if ($mediaType === 'document' && empty($decodedMediaData['name'])) {
                    $validator->errors()->add('media_data', 'Nome do arquivo é obrigatório para documentos.');
                    return;
                }
            }

            // Se não é mídia, validar se há mensagem de texto
            if (empty($mediaType) || $mediaType === 'text') {
                if (empty($message)) {
                    $validator->errors()->add('message', 'Mensagem de texto é obrigatória para campanhas de texto.');
                    return;
                }
            }
        });
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
        // Debug: Log all incoming data before validation
        \Log::info('🔍 MassSendingRequest validation data', [
            'has_media_type' => $this->has('media_type'),
            'has_media_data' => $this->has('media_data'),
            'media_type' => $this->input('media_type'),
            'media_data' => $this->input('media_data'),
            'media_data_type' => gettype($this->input('media_data')),
            'all_data' => $this->all()
        ]);

        // Sanitize input data
        $this->merge([
            'name' => strip_tags(trim($this->name ?? '')),
            'message' => strip_tags(trim($this->message ?? '')),
            'media_caption' => strip_tags(trim($this->media_caption ?? '')),
            'manual_numbers' => trim($this->manual_numbers ?? ''),
        ]);
    }
}
