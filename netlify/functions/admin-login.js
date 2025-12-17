const bcrypt = require('bcrypt');
const crypto = require('crypto');

function base64UrlEncode(input) {
  return Buffer.from(input).toString('base64').replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
}

function signToken(payload, secret) {
  const payloadPart = base64UrlEncode(JSON.stringify(payload));
  const sigPart = base64UrlEncode(crypto.createHmac('sha256', secret).update(payloadPart).digest());
  return `${payloadPart}.${sigPart}`;
}

exports.handler = async (event, context) => {
  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      body: 'Method Not Allowed',
    };
  }

  const { username, password } = JSON.parse(event.body);

  // Use env vars for credentials
  const adminUsername = process.env.ADMIN_USERNAME || 'admin';
  const adminPasswordHash = process.env.ADMIN_PASSWORD_HASH || '$2b$10$defaultHashedPassword'; // Replace with actual hash
  const tokenSecret = process.env.ADMIN_TOKEN_SECRET;

  if (username === adminUsername) {
    const isValid = await bcrypt.compare(password, adminPasswordHash);
    if (isValid) {
      if (!tokenSecret) {
        return {
          statusCode: 500,
          body: JSON.stringify({ success: false, message: 'Server misconfigured' }),
        };
      }

      const token = signToken(
        { u: adminUsername, exp: Date.now() + 1000 * 60 * 60 * 8 },
        tokenSecret
      );

      return {
        statusCode: 200,
        body: JSON.stringify({ success: true, message: 'Login successful', token }),
      };
    }
  }

  return {
    statusCode: 200,
    body: JSON.stringify({ success: false, message: 'Invalid credentials' }),
  };
};