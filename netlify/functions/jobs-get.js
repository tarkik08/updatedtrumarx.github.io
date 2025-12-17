const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');

const headers = {
  'Content-Type': 'application/json',
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
  'Access-Control-Allow-Methods': 'GET, OPTIONS',
};

exports.handler = async (event) => {
  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod !== 'GET') {
    return { statusCode: 405, headers, body: JSON.stringify({ error: 'Method Not Allowed' }) };
  }

  try {
    // Google Sheets API setup
    console.log('Fetching jobs from Google Sheets...');
    const serviceAccountAuth = new JWT({
      email: process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      key: process.env.GOOGLE_PRIVATE_KEY.replace(/\\n/g, '\n'),
      scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    const doc = new GoogleSpreadsheet(process.env.GOOGLE_SPREADSHEET_ID, serviceAccountAuth);
    await doc.loadInfo();
    const sheet = doc.sheetsByIndex[0]; // First sheet

    // Get all rows (skip header row)
    const rows = await sheet.getRows();
    console.log(`Fetched ${rows.length} rows from Google Sheets`);

    // Convert to job objects
    const jobs = rows.map((row, index) => {
      // Get the header row to map columns correctly
      const headers = sheet.headerValues || [];
      const job = {};

      // Map headers to row data
      headers.forEach((header, i) => {
        job[header] = row._rawData[i] || '';
      });

      return {
        id: index + 1,
        title: job.title || '',
        description: job.description || '',
        type: job.type || '',
        deadline: job.deadline || '',
        created_at: job.created_at || new Date().toISOString()
      };
    });

    console.log('Jobs data:', JSON.stringify(jobs, null, 2));
    return { 
      statusCode: 200, 
      headers,
      body: JSON.stringify({ jobs }) 
    };
  } catch (error) {
    console.error('Error in jobs-get:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({ 
        error: 'Failed to load jobs',
        details: error.message 
      }),
    };
  }
};