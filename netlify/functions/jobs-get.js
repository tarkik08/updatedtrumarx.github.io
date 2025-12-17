const fs = require('fs');
const path = require('path');

const headers = {
  'Content-Type': 'application/json',
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
  'Access-Control-Allow-Methods': 'GET, OPTIONS',
};

function readSeedJobs() {
  try {
    const filePath = path.join(__dirname, '..', '..', 'data', 'careers.json');
    const raw = fs.readFileSync(filePath, 'utf8');
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) return parsed;
    if (parsed && Array.isArray(parsed.jobs)) return parsed.jobs;
    return [];
  } catch {
    return [];
  }
}

exports.handler = async (event) => {
  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod !== 'GET') {
    return { statusCode: 405, headers, body: JSON.stringify({ error: 'Method Not Allowed' }) };
  }

  try {
    const { getStore } = await import('@netlify/blobs');
    const store = getStore('careers');

    let jobs = await store.get('jobs', { type: 'json' });

    if (jobs === null) {
      jobs = readSeedJobs();
      await store.setJSON('jobs', jobs);
    }

    return { statusCode: 200, headers, body: JSON.stringify({ jobs }) };
  } catch (error) {
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({ error: 'Failed to load jobs' }),
    };
  }
};
