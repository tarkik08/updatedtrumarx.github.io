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
  const adminPasswordHash = process.env.ADMIN_PASSWORD_HASH || 'password'; // Plain for now, hash later

  if (username === adminUsername && password === adminPasswordHash) {
    return {
      statusCode: 200,
      body: JSON.stringify({ success: true, message: 'Login successful' }),
    };
  } else {
    return {
      statusCode: 401,
      body: JSON.stringify({ success: false, message: 'Invalid credentials' }),
    };
  }
};