const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');

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
    // Google Sheets API setup
    const serviceAccountAuth = new JWT({
      email: process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      key: process.env.GOOGLE_PRIVATE_KEY.replace(/\\n/g, '\n'),
      scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.GOOGLE_SPREADSHEET_ID);
    await doc.useServiceAccountAuth(serviceAccountAuth);

    await doc.loadInfo();
    const sheet = doc.sheetsByIndex[0]; // First sheet

    // Get all rows
    const rows = await sheet.getRows();
    
    // Convert to job objects
    const jobs = rows.map((row, index) => ({
      id: index + 1,
      title: row.title || '',
      description: row.description || '',
      type: row.type || '',
      deadline: row.deadline || '',
      created_at: row.created_at || ''
    }));

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
