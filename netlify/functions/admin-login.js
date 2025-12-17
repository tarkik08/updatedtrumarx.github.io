const bcrypt = require('bcrypt');

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

  if (username === adminUsername) {
    const isValid = await bcrypt.compare(password, adminPasswordHash);
    if (isValid) {
      return {
        statusCode: 200,
        body: JSON.stringify({ success: true, message: 'Login successful' }),
      };
    }
  }

  return {
    statusCode: 401,
    body: JSON.stringify({ success: false, message: 'Invalid credentials' }),
  };
};