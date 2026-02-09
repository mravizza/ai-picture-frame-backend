<?php

namespace App;

class ImageAnalyzer
{
    public static function analyze(string $filePath, string $mime): ?string
    {
        $apiKey = config('openai_api_key');
        if (empty($apiKey)) {
            return null;
        }

        $imageData = file_get_contents($filePath);
        if ($imageData === false) {
            return null;
        }

        $base64 = base64_encode($imageData);
        $dataUri = 'data:' . $mime . ';base64,' . $base64;

        $payload = [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Describe this image in English in 2-3 sentences. Focus on the people, setting, and mood.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $dataUri,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 300,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }
}
