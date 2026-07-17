<?php
namespace App\Services;

final class AiService {
    private SecretService $secrets;

    public function __construct(?SecretService $secrets = null) {
        $this->secrets = $secrets ?? new SecretService();
    }

    public function call(array $messages, array $options = []): string {
        $mc = $this->secrets->getModelConfig();
        if (empty($mc['apiKey'])) {
            return 'AI model not configured. Go to Admin → Integrations and set api_endpoint, agent_api_key, and agent_model.';
        }

        $endpoint = rtrim($mc['endpoint'] ?? 'https://api.openai.com/v1', '/');
        $model = $mc['model'] ?? 'gemma-4-31b-it';
        $key = $mc['apiKey'] ?? '';
        $provider = $mc['provider'] ?? 'openai';
        $timeout = $options['timeout'] ?? 60;
        $maxTokens = $options['max_tokens'] ?? 1024;
        $temperature = $options['temperature'] ?? 0.3;

        if ($provider === 'google') {
            return $this->callGoogle($endpoint, $model, $key, $messages, $timeout, $maxTokens, $temperature);
        }

        return $this->callOpenAI($endpoint, $model, $key, $provider, $messages, $timeout, $maxTokens, $temperature);
    }

    public static function conversationKey(string $owner, string $repo, int $number): string {
        return hash('sha256', "{$owner}/{$repo}#{$number}");
    }

    private function callGoogle(string $endpoint, string $model, string $key, array $messages, int $timeout, int $maxTokens, float $temperature): string {
        $parts = [];
        foreach ($messages as $msg) {
            $parts[] = ['text' => ($msg['role'] ?? 'user') . ': ' . ($msg['content'] ?? '')];
        }
        $url = $endpoint . '/' . rawurlencode($model) . ':generateContent';
        $payload = json_encode([
            'contents' => [['parts' => $parts]],
            'generationConfig' => ['temperature' => $temperature, 'maxOutputTokens' => $maxTokens],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout > 10 ? 10 : (int)($timeout / 2),
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status !== 200 || $body === false) return "API error (HTTP {$status}). Check model config.";
        $result = json_decode($body, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';
    }

    private function callOpenAI(string $endpoint, string $model, string $key, string $provider, array $messages, int $timeout, int $maxTokens, float $temperature): string {
        $url = $endpoint . '/chat/completions';
        $payload = json_encode([
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        $ch = curl_init($url);
        $headers = ['Content-Type: application/json'];
        if ($provider === 'anthropic') {
            $headers[] = 'x-api-key: ' . $key;
            $headers[] = 'anthropic-version: 2023-06-01';
        } else {
            $headers[] = 'Authorization: Bearer ' . $key;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout > 10 ? 10 : (int)($timeout / 2),
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status !== 200 || $body === false) return "API error (HTTP {$status}). Check your endpoint/key/model in Admin → Integrations.";
        $result = json_decode($body, true);
        return $result['choices'][0]['message']['content'] ?? 'No response.';
    }
}
