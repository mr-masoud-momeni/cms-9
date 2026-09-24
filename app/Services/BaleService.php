<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BaleService
{
    private function request(string $method, array $payload = []): array
    {
        $token = config('services.bale.bot_token');

        if (!$token) {
            throw new RuntimeException('BALE_BOT_TOKEN تنظیم نشده است.');
        }

        $url = "https://tapi.bale.ai/bot{$token}/{$method}";

        try {
            $response = Http::timeout(10)
                ->retry(
                    3,
                    500,
                    function ($exception, $request) {
                        return $exception instanceof \Throwable;
                    },
                    throw: false
                )
                ->post($url, $payload);
        } catch (\Throwable $e) {
            Log::error('Bale API request failed', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        $body = $response->body();
        $ok = $response->json('ok');

        if (!$response->successful() || !$ok) {
            Log::error('Bale API returned an error', [
                'method' => $method,
                'status' => $response->status(),
                'response' => $body,
            ]);

            throw new RuntimeException(
                "Bale API error [{$method}] HTTP {$response->status()}: {$body}"
            );
        }

        Log::info('Bale API request succeeded', [
            'method' => $method,
            'status' => $response->status(),
        ]);

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
                ->retry(
                    3,
                    750,
                    function ($exception, $request) {
                        return $exception instanceof \Throwable;
                    },
                    throw: false
                )
                ->attach('photo', $handle, basename($photoPath))
                ->post("https://tapi.bale.ai/bot{$token}/sendPhoto", $payload);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        $body = $response->body();

        if (!$response->successful() || !$response->json('ok')) {
            Log::error('Bale sendPhoto returned an error', [
                'status' => $response->status(),
                'response' => $body,
            ]);

            throw new RuntimeException(
                "Bale API error [sendPhoto] HTTP {$response->status()}: {$body}"
            );
        }

        Log::info('Bale sendPhoto succeeded', [
            'status' => $response->status(),
        ]);

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
