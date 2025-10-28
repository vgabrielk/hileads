<?php

namespace Database\Factories;

use App\Models\MassSending;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MassSendingFactory extends Factory
{
    protected $model = MassSending::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'message' => $this->faker->text(200),
            'message_type' => 'text',
            'media_data' => null,
            'status' => 'draft',
            'contact_ids' => [],
            'wuzapi_participants' => [],
            'total_contacts' => 0,
            'total_recipients' => 0,
            'sent_count' => 0,
            'delivered_count' => 0,
            'read_count' => 0,
            'replied_count' => 0,
            'failed_count' => 0,
            'scheduled_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'cancelled_at' => null,
            'notes' => null,
        ];
    }

    public function withImage(): static
    {
        return $this->state([
            'message_type' => 'image',
            'media_data' => [
                'name' => 'test-image.jpg',
                'type' => 'image/jpeg',
                'base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8A8A',
                'size' => 1024
            ]
        ]);
    }

    public function withVideo(): static
    {
        return $this->state([
            'message_type' => 'video',
            'media_data' => [
                'name' => 'test-video.mp4',
                'type' => 'video/mp4',
                'base64' => 'data:video/mp4;base64,AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAAIZnJlZQAAAr1tZGF0AAACrgYF//+q3EXpvebZSLeWLNgg2SPu73gyNjQgLSBjb3JlIDE1NSByMjkxNyAwYjg2ZDk2IC0gSC4yNjQvTVBFRy00IEFWQyBjb2RlYyAtIENvcHlsZWZ0IDIwMDMtMjAxOCAtIGh0dHA6Ly93d3cudmlkZW9sYW4ub3JnL3gyNjQuaHRtbCAtIG9wdGlvbnM6IGNhYmFjPTEgcmVmPTMgZGVibG9jaz0xOjA6MCBhbmFseXNlPTB4MzoweDExMyBtZT1oZXggc3VibWU9NyBwc3k9MSBwc3ltcj0wLjAwMDAwMDAwIHByb2Q9MS4wMDAwMDAwMCBwcm9kcz0xLjAwMDAwMDAw',
                'size' => 2048
            ]
        ]);
    }

    public function withDocument(): static
    {
        return $this->state([
            'message_type' => 'document',
            'media_data' => [
                'name' => 'test-document.pdf',
                'type' => 'application/pdf',
                'base64' => 'data:application/pdf;base64,JVBERi0xLjQKJcfsj6IKNSAwIG9iago8PAovVHlwZSAvUGFnZQovUGFyZW50IDMgMCBSCi9SZXNvdXJjZXMgPDwKL0ZvbnQgPDwKL0YxIDYgMCBSCj4+Cj4+Ci9NZWRpYUJveCBbMCAwIDU5NSA4NDJdCi9Db250ZW50cyA3IDAgUgo+PgplbmRvYmoKNiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EKPj4KZW5kb2JqCjcgMCBvYmoKPDwKL0xlbmd0aCA0NAo+PgpzdHJlYW0KQlQKNjcyIDAgMCA1OTUgODQyIFRkCihUZXN0IERvY3VtZW50KSBUagpFVApFTQplbmRzdHJlYW0KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFs0IDAgUl0KL0NvdW50IDEKPj4KZW5kb2JqCjEgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDMgMCBSCj4+CmVuZG9iagp4cmVmCjAgOAowMDAwMDAwMDAwIDY1NTM1IGYKMDAwMDAwMDAwOSAwMDAwMCBuCjAwMDAwMDAwNTggMDAwMDAgbgowMDAwMDAwMTE1IDAwMDAwIG4KMDAwMDAwMDI2OCAwMDAwMCBuCjAwMDAwMDAzODcgMDAwMDAgbgowMDAwMDAwNDQ0IDAwMDAwIG4KMDAwMDAwMDU5MyAwMDAwMCBuCnRyYWlsZXIKPDwKL1NpemUgOAovUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKNjgxCiUlRU9G',
                'size' => 512
            ]
        ]);
    }

    public function withInvalidMedia(): static
    {
        return $this->state([
            'message_type' => 'image',
            'media_data' => [
                'name' => 'test-image.jpg',
                'type' => 'image/jpeg',
                'base64' => '', // Empty base64
                'size' => 0
            ]
        ]);
    }

    public function withEmptyMediaData(): static
    {
        return $this->state([
            'message_type' => 'image',
            'media_data' => []
        ]);
    }

    public function withNullMediaData(): static
    {
        return $this->state([
            'message_type' => 'image',
            'media_data' => null
        ]);
    }
}
