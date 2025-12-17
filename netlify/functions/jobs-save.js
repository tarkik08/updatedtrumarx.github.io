// Version 2.0 - Using file-based storage instead of Netlify Blobs
const crypto = require('crypto');

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

    // Use file-based storage instead of Netlify Blobs
    const fs = require('fs').promises;
    const path = require('path');
    
    try {
      const dataDir = path.join(__dirname, '..', '..', 'data');
      const filePath = path.join(dataDir, 'careers.json');
      
      // Ensure data directory exists
      await fs.mkdir(dataDir, { recursive: true });
      
      // Write jobs to file
      await fs.writeFile(filePath, JSON.stringify({ jobs }, null, 2));
    } catch (fileError) {
      console.error('File storage error:', fileError);
      throw fileError;
    }

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
