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

    // Hardcoded configuration to bypass environment variable issues
    console.log('Using hardcoded configuration');
    console.log('NETLIFY_AUTH_TOKEN exists:', !!process.env.NETLIFY_AUTH_TOKEN);
    
    const { getStore } = await import('@netlify/blobs');
    const store = getStore('careers', {
      siteID: '12d723af-3f91-47e3-9cda-ff6f24152a48', // Correct Netlify site ID
      token: process.env.NETLIFY_AUTH_TOKEN // Still need auth token
    });

    await store.setJSON('jobs', jobs);

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
