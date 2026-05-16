exports.handler = async (event) => {
  const isTest = event.queryStringParameters?.test === '1';
  try {
    const res = await fetch(
      'https://publicreporting.cftc.gov/api/views/6dqn-4d3e/rows.json?accessType=DOWNLOAD&$limit=50',
      { headers: { 'User-Agent': 'JtradesFX/1.0' } }
    );
    if (!res.ok) throw new Error(`CFTC ${res.status}`);
    const data = await res.json();
    return ok(isTest ? { ok: true, rows: data?.data?.length || 0 } : data, 3600);
  } catch (e) {
    return ok({ error: e.message, data: null });
  }
};

function ok(data, cache) {
  return {
    statusCode: 200,
    headers: { 'Content-Type': 'application/json', 'Cache-Control': `public, max-age=${cache||0}` },
    body: JSON.stringify(data),
  };
}
