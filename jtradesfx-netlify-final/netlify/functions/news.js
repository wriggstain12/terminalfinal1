exports.handler = async (event) => {
  const key = process.env.NEWS_API_KEY;
  const q = event.queryStringParameters?.q || 'forex OR "central bank" OR "interest rate" OR EUR OR USD OR JPY';
  if (!key) return err('NEWS_API_KEY not set');
  try {
    const url = new URL('https://newsapi.org/v2/everything');
    url.searchParams.set('q', q);
    url.searchParams.set('language', 'en');
    url.searchParams.set('sortBy', 'publishedAt');
    url.searchParams.set('pageSize', '20');
    url.searchParams.set('apiKey', key);
    const res = await fetch(url.toString());
    const data = await res.json();
    return ok(data, 300);
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
