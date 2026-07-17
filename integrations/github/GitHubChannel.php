<?php
namespace App\Integrations\GitHub;
use App\Services\AgentRuntimeService;

final class GitHubChannel {
    private string $webhookSecret;
    private string $token;
    private string $owner;
    private string $repo;

    public function __construct(string $webhookSecret = '', string $token = '', string $owner = 'bapXai', string $repo = '') {
        $this->webhookSecret = $webhookSecret;
        $this->token = $token;
        $this->owner = $owner;
        $this->repo = $repo;
    }

    public function verifyWebhook(array $payload, string $signature): bool {
        if ($this->webhookSecret === '') return true;
        $expected = 'sha256=' . hash_hmac('sha256', json_encode($payload), $this->webhookSecret);
        return hash_equals($expected, $signature);
    }

    public function dispatch(array $payload, string $event): array {
        if ($event === 'ping') {
            return ['message' => 'pong', 'event' => $event];
        }
        $runtime = new AgentRuntimeService();
        return $runtime->processWebhook($payload, $event);
    }

    public function apiCall(string $method, string $endpoint, ?array $body = null): array {
        $url = 'https://api.github.com/' . ltrim($endpoint, '/');
        $headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: bapX-github-channel/1.0',
        ];
        if ($this->token !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 20,
        ];
        if ($method === 'POST') {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($body ?? []);
        } elseif ($method === 'PATCH') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PATCH';
            $opts[CURLOPT_POSTFIELDS] = json_encode($body ?? []);
        } elseif ($method !== 'GET') {
            $opts[CURLOPT_CUSTOMREQUEST] = $method;
            if ($body !== null) {
                $opts[CURLOPT_POSTFIELDS] = json_encode($body);
            }
        }
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        if ($response === false || $response === '') {
            throw new \RuntimeException('GitHub API error: ' . ($error ?: 'empty response'), $status ?: 500);
        }
        $decoded = json_decode($response, true);
        if ($status >= 300) {
            $message = $decoded['message'] ?? $error ?: 'GitHub API request failed';
            throw new \RuntimeException($message, $status);
        }
        return $decoded ?? [];
    }

    public function createIssue(string $title, string $body, array $labels = []): array {
        return $this->apiCall('POST', "repos/{$this->owner}/{$this->repo}/issues", [
            'title' => $title,
            'body' => $body,
            'labels' => $labels,
        ]);
    }

    public function createComment(int $issueNumber, string $body): array {
        return $this->apiCall('POST', "repos/{$this->owner}/{$this->repo}/issues/{$issueNumber}/comments", [
            'body' => $body,
        ]);
    }

    public function updateIssue(int $issueNumber, array $data): array {
        return $this->apiCall('PATCH', "repos/{$this->owner}/{$this->repo}/issues/{$issueNumber}", $data);
    }

    public function getIssue(int $issueNumber): array {
        return $this->apiCall('GET', "repos/{$this->owner}/{$this->repo}/issues/{$issueNumber}");
    }
}
