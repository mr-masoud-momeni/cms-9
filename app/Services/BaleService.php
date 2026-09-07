<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BaleService
{
    private function request(string $method, array $payload = []): array
    {
        $token = config('services.bale.bot_token');

        if (!$token) {
            throw new RuntimeException('BALE_BOT_TOKEN تنظیم نشده است.');
        }

        $response = Http::timeout(10)
            ->post("https://tapi.bale.ai/bot{$token}/{$method}", $payload);

        if (!$response->successful() || !$response->json('ok')) {
            throw new RuntimeException(
                'Bale API error: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function sendMessage(int|string $chatId, string $text, array $replyMarkup = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }

        return $this->request('sendMessage', $payload);
    }

    public function sendPhoto(
        int|string $chatId,
        string $photoPath,
        string $caption = '',
        array $replyMarkup = []
    ): array {
        if (!is_file($photoPath) || !is_readable($photoPath)) {
            throw new RuntimeException("فایل رسید پیدا نشد: {$photoPath}");
        }

        $token = config('services.bale.bot_token');

        if (!$token) {
            throw new RuntimeException('BALE_BOT_TOKEN تنظیم نشده است.');
        }

        $payload = [
            'chat_id' => (string) $chatId,
            'caption' => $caption,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $handle = fopen($photoPath, 'r');

        try {
            $response = Http::timeout(20)
                ->attach('photo', $handle, basename($photoPath))
                ->post("https://tapi.bale.ai/bot{$token}/sendPhoto", $payload);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        if (!$response->successful() || !$response->json('ok')) {
            throw new RuntimeException(
                'Bale API error: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): array
    {
        return $this->request('answerCallbackQuery', array_filter([
            'callback_query_id' => $callbackQueryId,
            'text' => $text ?: null,
        ], fn ($value) => $value !== null));
    }

    public function editMessageReplyMarkup(int|string $chatId, int $messageId, array $replyMarkup = []): array
    {
        return $this->request('editMessageReplyMarkup', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => $replyMarkup ?: ['inline_keyboard' => []],
        ]);
    }
}
