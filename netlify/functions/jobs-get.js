const fs = require('fs').promises;
const path = require('path');

const headers = {
  'Content-Type': 'application/json',
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
  'Access-Control-Allow-Methods': 'GET, OPTIONS',
};

async function readJobsFromFile() {
  try {
    const filePath = path.join(__dirname, '..', '..', 'data', 'careers.json');
    const raw = await fs.readFile(filePath, 'utf8');
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
    let jobs = await readJobsFromFile();

    return { statusCode: 200, headers, body: JSON.stringify({ jobs }) };
  } catch (error) {
    console.error('Error in jobs-get:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({ error: 'Failed to load jobs' }),
    };
  }
};
