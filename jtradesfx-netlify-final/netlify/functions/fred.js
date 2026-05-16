exports.handler = async (event) => {
  const key = process.env.FRED_API_KEY;
  const series = event.queryStringParameters?.series;
  const isTest = event.queryStringParameters?.test === '1';
  if (!key) return err('FRED_API_KEY not set');
  if (!series) return err('series param required');
  try {
    const url = isTest
      ? `https://api.stlouisfed.org/fred/series?series_id=${series}&api_key=${key}&file_type=json`
      : `https://api.stlouisfed.org/fred/series/observations?series_id=${series}&api_key=${key}&file_type=json&limit=13&sort_order=desc`;
    const res = await fetch(url);
    const data = await res.json();
    return ok(data, 3600);
  } catch (e) { return err(e.message); }
};

function ok(data, cache) {
  return {
    statusCode: 200,
    headers: { 'Content-Type': 'application/json', 'Cache-Control': `public, max-age=${cache||0}` },
    body: JSON.stringify(data),
  };
}
function err(msg) {
  return { statusCode: 500, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ error: msg }) };
}
