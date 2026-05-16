exports.handler = async (event) => {
  const base = event.queryStringParameters?.base || 'USD';
  const key = process.env.EXCHANGERATE_API_KEY;
  if (!key) return err('EXCHANGERATE_API_KEY not set');
  try {
    const res = await fetch(`https://v6.exchangerate-api.com/v6/${key}/latest/${base}`);
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
