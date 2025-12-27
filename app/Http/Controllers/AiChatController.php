<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $apiKey = config('services.openai.key');
        $model  = config('services.openai.model', 'gpt-4o-mini');

        if (!$apiKey) {
            return response()->json(['ok' => false, 'error' => 'Missing OPENAI_API_KEY'], 500);
        }

        // Responses API (recommended)
        // Docs: POST /v1/responses :contentReference[oaicite:4]{index=4}
        $payload = [
            'model' => $model,
            'input' => [
                [
                    'role' => 'system',
                    'content' => [
                        ['type' => 'text', 'text' => 'Bạn là chatbot hỗ trợ khách hàng cho shop đồ công nghệ. Trả lời ngắn gọn, rõ ràng, lịch sự.']
                    ],
                ],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $request->message]
                    ],
                ],
            ],
        ];

        $res = Http::withToken($apiKey)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', $payload);

        if (!$res->successful()) {
            return response()->json([
                'ok' => false,
                'error' => 'OpenAI request failed',
                'status' => $res->status(),
                'detail' => $res->json(),
            ], 500);
        }

        $json = $res->json();

        // Bóc text ra an toàn (Responses API trả output dạng mảng)
        $reply = '';
        $contents = data_get($json, 'output.0.content', []);
        foreach ($contents as $part) {
            $reply .= (string) data_get($part, 'text', '');
        }
        $reply = trim($reply);

        return response()->json([
            'ok' => true,
            'reply' => $reply ?: '(Không có phản hồi)',
        ]);
    }
}
