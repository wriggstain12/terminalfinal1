<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=300');

$key = getenv('NEWS_API_KEY') ?: '80b555adc7714ecbab2017343a85abce';
$q   = isset($_GET['q']) ? urlencode(strip_tags($_GET['q'])) : urlencode('forex OR "central bank" OR "interest rate" OR EUR OR USD OR JPY');

$url = "https://newsapi.org/v2/everything?q={$q}&language=en&sortBy=publishedAt&pageSize=20&apiKey={$key}";

$ctx = stream_context_create(['http' => [
    'timeout'       => 10,
    'ignore_errors' => true,
    'method'        => 'GET',
    'header'        => "User-Agent: JtradesFX/1.0\r\n",
]]);

$body = @file_get_contents($url, false, $ctx);

if ($body === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach NewsAPI']);
    exit;
}

echo $body;
