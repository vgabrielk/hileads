<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\CampaignLogger;
use App\Helpers\JsonSanitizer;
use App\Helpers\MediaJsonHelper;

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
            CampaignLogger::startProcess('MassSendingRequest Validation', [
                'user_id' => auth()->id(),
                'request_method' => $this->method(),
                'request_url' => $this->url()
            ]);

            $mediaType = $this->input('media_type');
            $mediaData = $this->input('media_data');
            $message = $this->input('message');

            CampaignLogger::info('Dados recebidos para validação', [
                'media_type' => $mediaType,
                'has_media_data' => !empty($mediaData),
                'media_data_type' => gettype($mediaData),
                'has_message' => !empty($message),
                'message_length' => strlen($message ?? '')
            ]);

            // Se é uma campanha de mídia, validar media_data
            if (in_array($mediaType, ['image', 'video', 'audio', 'document'])) {
                CampaignLogger::info("Validando campanha de mídia: {$mediaType}");

                if (empty($mediaData)) {
                    CampaignLogger::error('Dados da mídia estão vazios');
                    $validator->errors()->add('media_data', 'Dados da mídia são obrigatórios para campanhas de mídia.');
                    return;
                }

                // Decodificar e validar estrutura do media_data
                $decodedMediaData = null;
                
                if (is_string($mediaData)) {
                    // Obter informações de erro antes da sanitização
                    $errorInfo = JsonSanitizer::getErrorInfo($mediaData);
                    
                    CampaignLogger::debug('Decodificando media_data', [
                        'is_string' => true,
                        'json_length' => strlen($mediaData),
                        'has_control_chars' => $errorInfo['has_control_chars'],
                        'control_chars_count' => $errorInfo['control_chars_count'],
                        'json_preview' => $errorInfo['json_preview']
                    ]);
                    
                    // Tentar decodificar com sanitização
                    $decodedMediaData = JsonSanitizer::decode($mediaData, true);
                    
                    if ($decodedMediaData === null) {
                        CampaignLogger::error('media_data não é um JSON válido após sanitização', [
                            'original_error' => $errorInfo,
                            'media_data_length' => strlen($mediaData),
                            'media_data_preview' => substr($mediaData, 0, 500)
                        ]);
                        $validator->errors()->add('media_data', 'Dados da mídia devem ser um JSON válido.');
                        return;
                    }
                } else {
                    $decodedMediaData = $mediaData;
                }
                
                CampaignLogger::debug('media_data decodificado com sucesso', [
                    'decoded_type' => gettype($decodedMediaData),
                    'is_array' => is_array($decodedMediaData),
                    'array_keys' => is_array($decodedMediaData) ? array_keys($decodedMediaData) : []
                ]);

                CampaignLogger::mediaData('Estrutura do media_data decodificado', $decodedMediaData);

                // Validar campos obrigatórios do media_data
                if (empty($decodedMediaData['base64'])) {
                    CampaignLogger::error('Campo base64 está vazio', [
                        'available_keys' => array_keys($decodedMediaData),
                        'base64_exists' => isset($decodedMediaData['base64']),
                        'base64_empty' => empty($decodedMediaData['base64'])
                    ]);
                    $validator->errors()->add('media_data', 'Dados base64 da mídia são obrigatórios.');
                    return;
                }

                // Validar tamanho do base64 (máximo 5MB para compatibilidade)
                $base64Length = strlen($decodedMediaData['base64']);
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                CampaignLogger::validation('Validando tamanho do Base64', [
                    'base64_length' => $base64Length,
                    'max_size' => $maxSize,
                    'size_mb' => round($base64Length / 1024 / 1024, 2),
                    'max_size_mb' => round($maxSize / 1024 / 1024, 2),
                    'is_within_limit' => $base64Length <= $maxSize
                ]);

                if ($base64Length > $maxSize) {
                    CampaignLogger::error('Arquivo muito grande', [
                        'size_mb' => round($base64Length / 1024 / 1024, 2),
                        'max_size_mb' => round($maxSize / 1024 / 1024, 2)
                    ]);
                    $validator->errors()->add('media_data', 'Arquivo muito grande. Tamanho máximo permitido: 5MB.');
                    return;
                }

                // Validar se base64 é válido
                $isValidFormat = preg_match('/^data:[^;]+;base64,/', $decodedMediaData['base64']);
                
                CampaignLogger::validation('Validando formato do Base64', [
                    'is_valid_format' => $isValidFormat,
                    'base64_prefix' => substr($decodedMediaData['base64'], 0, 50),
                    'regex_match' => $isValidFormat
                ]);

                if (!$isValidFormat) {
                    CampaignLogger::error('Formato Base64 inválido', [
                        'base64_prefix' => substr($decodedMediaData['base64'], 0, 100),
                        'expected_format' => 'data:type;base64,'
                    ]);
                    $validator->errors()->add('media_data', 'Formato base64 inválido. Deve incluir o prefixo data:type;base64,');
                    return;
                }

                // Validar se não é formato WebP (não permitido)
                $isWebP = preg_match('/^data:image\/webp;base64,/', $decodedMediaData['base64']);
                
                CampaignLogger::validation('Validando formato de imagem', [
                    'is_webp' => $isWebP,
                    'base64_prefix' => substr($decodedMediaData['base64'], 0, 50),
                    'file_type' => $decodedMediaData['type'] ?? 'unknown'
                ]);

                if ($isWebP) {
                    CampaignLogger::error('Formato WebP não permitido', [
                        'base64_prefix' => substr($decodedMediaData['base64'], 0, 100),
                        'file_type' => $decodedMediaData['type'] ?? 'unknown'
                    ]);
                    $validator->errors()->add('media_data', 'Formato WebP não é permitido. Use JPG, PNG ou GIF.');
                    return;
                }

                // Validar integridade do Base64
                $base64Data = $decodedMediaData['base64'];
                
                CampaignLogger::validation('Validando integridade do Base64', [
                    'base64_length' => strlen($base64Data),
                    'is_valid_base64' => MediaJsonHelper::isValidBase64($base64Data)
                ]);

                if (!MediaJsonHelper::isValidBase64($base64Data)) {
                    CampaignLogger::error('Base64 corrompido ou inválido');
                    $validator->errors()->add('media_data', 'Dados base64 corrompidos ou inválidos.');
                    return;
                }

                // Para documentos, validar nome do arquivo
                if ($mediaType === 'document' && empty($decodedMediaData['name'])) {
                    CampaignLogger::error('Nome do arquivo ausente para documento', [
                        'available_keys' => array_keys($decodedMediaData)
                    ]);
                    $validator->errors()->add('media_data', 'Nome do arquivo é obrigatório para documentos.');
                    return;
                }

                CampaignLogger::validation('Validação de mídia concluída com sucesso', [
                    'media_type' => $mediaType,
                    'file_name' => $decodedMediaData['name'] ?? 'N/A',
                    'mime_type' => $decodedMediaData['type'] ?? 'N/A',
                    'size_mb' => round($base64Length / 1024 / 1024, 2)
                ]);
            }

            // Se não é mídia, validar se há mensagem de texto
            if (empty($mediaType) || $mediaType === 'text') {
                CampaignLogger::info('Validando campanha de texto');
                
                if (empty($message)) {
                    CampaignLogger::error('Mensagem de texto está vazia');
                    $validator->errors()->add('message', 'Mensagem de texto é obrigatória para campanhas de texto.');
                    return;
                }
                
                CampaignLogger::validation('Validação de texto concluída', [
                    'message_length' => strlen($message)
                ]);
            }

            CampaignLogger::endProcess('MassSendingRequest Validation', [
                'validation_passed' => $validator->errors()->isEmpty(),
                'error_count' => $validator->errors()->count()
            ]);
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
