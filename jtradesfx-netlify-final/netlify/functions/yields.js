exports.handler = async () => {
  try {
    const year = new Date().getFullYear();
    const url =
      `https://home.treasury.gov/resource-center/data-chart-center/interest-rates/daily-treasury-rates.csv/${year}/all` +
      `?type=daily_treasury_yield_curve&field_tdr_date_value=${year}&submit=submit&format=csv`;
    const res = await fetch(url, { headers: { 'User-Agent': 'JtradesFX/1.0' } });
    if (!res.ok) throw new Error(`Treasury ${res.status}`);
    const csv = await res.text();
    const lines = csv.trim().split('\n');
    if (lines.length < 2) throw new Error('Empty CSV');
    const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
    const last = lines[lines.length - 1].split(',').map(v => v.trim().replace(/"/g, ''));
    const row = {};
    headers.forEach((h, i) => (row[h] = last[i]));
    const get = (...keys) => {
      for (const k of keys) {
        const found = Object.keys(row).find(h => h.toLowerCase().includes(k.toLowerCase()));
        if (found && row[found]) return parseFloat(row[found]);
      }
      return null;
    };
    const yields = {
      date: row['Date'] || 'latest',
      y1mo: get('1 Mo'), y3mo: get('3 Mo'), y6mo: get('6 Mo'),
      y1yr: get('1 Yr'), y2yr: get('2 Yr'), y3yr: get('3 Yr'),
      y5yr: get('5 Yr'), y7yr: get('7 Yr'), y10yr: get('10 Yr'),
      y20yr: get('20 Yr'), y30yr: get('30 Yr'),
    };
    return ok(yields, 3600);
  } catch (e) {
    return ok({ error: e.message, date: 'fallback', y2yr: 4.85, y5yr: 4.52, y10yr: 4.41, y30yr: 4.58 });
  }
};

function ok(data, cache) {
  return {
    statusCode: 200,
    headers: { 'Content-Type': 'application/json', 'Cache-Control': `public, max-age=${cache||0}` },
    body: JSON.stringify(data),
  };
}
