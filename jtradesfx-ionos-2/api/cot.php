<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');

$test = isset($_GET['test']) && $_GET['test'] === '1';
$url  = 'https://publicreporting.cftc.gov/api/views/6dqn-4d3e/rows.json?accessType=DOWNLOAD&$limit=50';

$ctx = stream_context_create(['http' => [
    'timeout'       => 20,
    'ignore_errors' => true,
    'method'        => 'GET',
    'header'        => "User-Agent: JtradesFX/1.0\r\n",
]]);

$body = @file_get_contents($url, false, $ctx);

if ($body === false) {
    // Return graceful fallback so UI still renders
    echo json_encode([
        'error' => 'CFTC API unavailable',
        'data'  => null,
    ]);
    exit;
}

if ($test) {
    $data = json_decode($body, true);
    echo json_encode(['ok' => true, 'rows' => count($data['data'] ?? [])]);
    exit;
}

echo $body;
