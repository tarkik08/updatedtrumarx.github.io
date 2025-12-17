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
    // Manual Netlify Blobs configuration with hardcoded site ID
    const { getStore } = await import('@netlify/blobs');
    const store = getStore('careers', {
      siteID: '12d723af-3f91-47e3-9cda-ff6f24152a48', // Correct Netlify site ID
      token: process.env.NETLIFY_AUTH_TOKEN || process.env.API_TOKEN
    });

    let jobs = await store.get('jobs', { type: 'json' });

    if (jobs === null) {
      jobs = await readJobsFromFile();
      await store.setJSON('jobs', jobs);
    }

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
