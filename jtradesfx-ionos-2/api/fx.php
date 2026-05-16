<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=300');

$key  = getenv('EXCHANGERATE_API_KEY') ?: '6c30cb730645e2f502c71d4f';
$base = isset($_GET['base']) ? preg_replace('/[^A-Z]/', '', strtoupper($_GET['base'])) : 'USD';

$url = "https://v6.exchangerate-api.com/v6/{$key}/latest/{$base}";

$ctx = stream_context_create(['http' => [
    'timeout'        => 8,
    'ignore_errors'  => true,
    'method'         => 'GET',
    'header'         => "User-Agent: JtradesFX/1.0\r\n",
]]);

$body = @file_get_contents($url, false, $ctx);

if ($body === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach ExchangeRate-API']);
    exit;
}

echo $body;
