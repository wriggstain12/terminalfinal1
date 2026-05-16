<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');

$key    = getenv('FRED_API_KEY') ?: 'bc015122c7283bcbcf19d2184a5dadeb';
$series = isset($_GET['series']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['series'])) : 'CPIAUCSL';
$test   = isset($_GET['test']) && $_GET['test'] === '1';

if ($test) {
    $url = "https://api.stlouisfed.org/fred/series?series_id={$series}&api_key={$key}&file_type=json";
} else {
    $url = "https://api.stlouisfed.org/fred/series/observations"
         . "?series_id={$series}&api_key={$key}&file_type=json&limit=13&sort_order=desc";
}

$ctx = stream_context_create(['http' => [
    'timeout'       => 12,
    'ignore_errors' => true,
    'method'        => 'GET',
    'header'        => "User-Agent: JtradesFX/1.0\r\n",
]]);

$body = @file_get_contents($url, false, $ctx);

if ($body === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach FRED API']);
    exit;
}

echo $body;
