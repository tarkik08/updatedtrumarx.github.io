const crypto = require('crypto');
const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');

const headers = {
  'Content-Type': 'application/json',
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
  'Access-Control-Allow-Methods': 'POST, OPTIONS',
};

function base64UrlEncode(input) {
  return Buffer.from(input).toString('base64').replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
}

function base64UrlDecode(input) {
  const padLength = (4 - (input.length % 4)) % 4;
  const padded = input + '='.repeat(padLength);
  const normalized = padded.replace(/-/g, '+').replace(/_/g, '/');
  return Buffer.from(normalized, 'base64').toString('utf8');
}

function verifyToken(token, secret) {
  if (!token || typeof token !== 'string') return null;
  const parts = token.split('.');
  if (parts.length !== 2) return null;

  const [payloadPart, sigPart] = parts;
  const expectedSig = base64UrlEncode(
    crypto.createHmac('sha256', secret).update(payloadPart).digest()
  );

  const a = Buffer.from(sigPart);
  const b = Buffer.from(expectedSig);
  if (a.length !== b.length || !crypto.timingSafeEqual(a, b)) return null;

  const payloadJson = base64UrlDecode(payloadPart);
  const payload = JSON.parse(payloadJson);
  if (!payload || typeof payload.exp !== 'number') return null;
  if (Date.now() > payload.exp) return null;

  return payload;
}

exports.handler = async (event) => {
  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod !== 'POST') {
    return { statusCode: 405, headers, body: JSON.stringify({ error: 'Method Not Allowed' }) };
  }

  try {
    const authHeader = event.headers.authorization || event.headers.Authorization || '';
    const token = authHeader.startsWith('Bearer ') ? authHeader.slice('Bearer '.length) : null;
    const secret = process.env.ADMIN_TOKEN_SECRET;

    console.log('ADMIN_TOKEN_SECRET exists:', !!secret);
    console.log('Token provided:', !!token);

    if (!secret) {
      return { statusCode: 500, headers, body: JSON.stringify({ error: 'Server misconfigured - missing ADMIN_TOKEN_SECRET' }) };
    }

    const payload = verifyToken(token, secret);
    if (!payload) {
      return { statusCode: 401, headers, body: JSON.stringify({ error: 'Unauthorized' }) };
    }

    const body = JSON.parse(event.body || '{}');
    const jobs = body.jobs;

    if (!Array.isArray(jobs)) {
      return { statusCode: 400, headers, body: JSON.stringify({ error: 'Invalid jobs payload' }) };
    }

    // Google Sheets API setup
    console.log('Using Google Sheets API');
    const serviceAccountAuth = new JWT({
      email: process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      key: process.env.GOOGLE_PRIVATE_KEY.replace(/\\n/g, '\n'),
      scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.GOOGLE_SPREADSHEET_ID);
    await doc.useServiceAccountAuth(serviceAccountAuth);

    await doc.loadInfo();
    const sheet = doc.sheetsByIndex[0]; // First sheet

    // Clear existing data and add new jobs
    await sheet.clear();
    await sheet.setHeaderRow(['id', 'title', 'description', 'type', 'deadline', 'created_at']);

    const rows = jobs.map((job, index) => [
      index + 1,
      job.title || '',
      job.description || '',
      job.type || '',
      job.deadline || '',
      new Date().toISOString()
    ]);

    await sheet.addRows(rows);

    return { statusCode: 200, headers, body: JSON.stringify({ success: true }) };
  } catch (error) {
    console.error('Detailed error in jobs-save:', error);
    return { 
      statusCode: 500, 
      headers, 
      body: JSON.stringify({ 
        error: 'Failed to save jobs', 
        details: error.message,
        stack: error.stack 
      }) 
    };
  }
};
