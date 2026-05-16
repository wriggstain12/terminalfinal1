<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600');

$year = date('Y');
$url  = "https://home.treasury.gov/resource-center/data-chart-center/interest-rates"
      . "/daily-treasury-rates.csv/{$year}/all"
      . "?type=daily_treasury_yield_curve&field_tdr_date_value={$year}&submit=submit&format=csv";

$ctx = stream_context_create(['http' => [
    'timeout'       => 15,
    'ignore_errors' => true,
    'method'        => 'GET',
    'header'        => "User-Agent: JtradesFX/1.0\r\n",
]]);

$csv = @file_get_contents($url, false, $ctx);

// Parse CSV to structured JSON
if ($csv !== false) {
    $lines = array_filter(explode("\n", trim($csv)));
    if (count($lines) >= 2) {
        $headers = str_getcsv(array_shift($lines));
        $last    = str_getcsv(end($lines));
        $row     = array_combine($headers, $last);

        function getYield($row, ...$keys) {
            foreach ($keys as $k) {
                foreach ($row as $col => $val) {
                    if (stripos($col, $k) !== false && $val !== '') {
                        return (float)$val;
                    }
                }
            }
            return null;
        }

        $y2  = getYield($row, '2 Yr', '2Yr');
        $y10 = getYield($row, '10 Yr', '10Yr');
        $spread = ($y2 && $y10) ? round($y10 - $y2, 3) : null;

        echo json_encode([
            'date'         => $row['Date'] ?? $row[array_key_first($row)] ?? 'latest',
            'y1mo'         => getYield($row, '1 Mo'),
            'y3mo'         => getYield($row, '3 Mo'),
            'y6mo'         => getYield($row, '6 Mo'),
            'y1yr'         => getYield($row, '1 Yr'),
            'y2yr'         => $y2,
            'y3yr'         => getYield($row, '3 Yr'),
            'y5yr'         => getYield($row, '5 Yr'),
            'y7yr'         => getYield($row, '7 Yr'),
            'y10yr'        => $y10,
            'y20yr'        => getYield($row, '20 Yr'),
            'y30yr'        => getYield($row, '30 Yr'),
            'spread_2s10s' => $spread,
        ]);
        exit;
    }
}

// Fallback
echo json_encode([
    'date'   => 'fallback',
    'error'  => 'Treasury CSV unavailable',
    'y2yr'   => 4.85,
    'y5yr'   => 4.52,
    'y10yr'  => 4.41,
    'y30yr'  => 4.58,
]);
