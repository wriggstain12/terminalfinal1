<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

$key = getenv('ANTHROPIC_API_KEY');
if (!$key) {
    http_response_code(500);
    echo json_encode(['error' => 'ANTHROPIC_API_KEY not configured. Add it in IONOS Deploy Now runtime variables.']);
    exit;
}

// Read and validate request body
$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body || !isset($body['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request body']);
    exit;
}

// Build safe payload — cap tokens to prevent runaway costs
$payload = [
    'model'      => $body['model']      ?? 'claude-sonnet-4-20250514',
    'max_tokens' => min($body['max_tokens'] ?? 1000, 2000),
    'messages'   => $body['messages'],
];
if (!empty($body['system'])) {
    $payload['system'] = $body['system'];
}

$json = json_encode($payload);

$ctx = stream_context_create(['http' => [
    'method'        => 'POST',
    'timeout'       => 30,
    'ignore_errors' => true,
    'header'        => implode("\r\n", [
        'Content-Type: application/json',
        "x-api-key: {$key}",
        'anthropic-version: 2023-06-01',
        'Content-Length: ' . strlen($json),
    ]),
    'content' => $json,
]]);

$body = @file_get_contents('https://api.anthropic.com/v1/messages', false, $ctx);

// Forward the HTTP status code
$status = 200;
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $h, $m)) {
            $status = (int)$m[1];
        }
    }
}

http_response_code($status);

if ($body === false) {
    echo json_encode(['error' => 'Failed to reach Anthropic API']);
    exit;
}

echo $body;
