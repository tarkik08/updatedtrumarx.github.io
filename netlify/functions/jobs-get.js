const { GoogleSpreadsheet } = require('google-spreadsheet');
const { JWT } = require('google-auth-library');

const headers = {
  'Content-Type': 'application/json',
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
  'Access-Control-Allow-Methods': 'GET, OPTIONS',
};

exports.handler = async (event) => {
  console.log('jobs-get function called');
  
  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod !== 'GET') {
    return { statusCode: 405, headers, body: JSON.stringify({ error: 'Method Not Allowed' }) };
  }

  try {
    console.log('Environment variables check:', {
      hasServiceAccountEmail: !!process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      hasPrivateKey: !!process.env.GOOGLE_PRIVATE_KEY,
      hasSpreadsheetId: !!process.env.GOOGLE_SPREADSHEET_ID,
    });

    if (!process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL) {
      throw new Error('GOOGLE_SERVICE_ACCOUNT_EMAIL is not set');
    }
    if (!process.env.GOOGLE_PRIVATE_KEY) {
      throw new Error('GOOGLE_PRIVATE_KEY is not set');
    }
    if (!process.env.GOOGLE_SPREADSHEET_ID) {
      throw new Error('GOOGLE_SPREADSHEET_ID is not set');
    }

    console.log('Initializing Google Sheets API...');
    const serviceAccountAuth = new JWT({
      email: process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      key: process.env.GOOGLE_PRIVATE_KEY.replace(/\\n/g, '\n'),
      scopes: ['https://www.googleapis.com/auth/spreadsheets'],
    });

    console.log('Loading spreadsheet...');
    const doc = new GoogleSpreadsheet(process.env.GOOGLE_SPREADSHEET_ID, serviceAccountAuth);
    
    try {
      await doc.loadInfo();
      console.log(`Successfully loaded spreadsheet: ${doc.title}`);
    } catch (error) {
      console.error('Failed to load spreadsheet. Possible issues:');
      console.error('- Incorrect spreadsheet ID');
      console.error('- Service account does not have access to the spreadsheet');
      console.error('Full error:', error);
      throw new Error(`Failed to load spreadsheet: ${error.message}`);
    }

    const sheet = doc.sheetsByIndex[0]; // First sheet
    console.log(`Using sheet: ${sheet.title}`);

    try {
      console.log('Loading header row...');
      await sheet.loadHeaderRow();
      console.log('Headers loaded:', sheet.headerValues);
      
      console.log('Fetching rows...');
      const rows = await sheet.getRows();
      console.log(`Fetched ${rows.length} rows`);

      const jobs = rows.map((row, index) => {
        const job = {};
        sheet.headerValues.forEach(header => {
          job[header] = row.get(header) || '';
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

      console.log(`Successfully processed ${jobs.length} jobs`);
      return { 
        statusCode: 200, 
        headers,
        body: JSON.stringify({ jobs }) 
      };

    } catch (error) {
      console.error('Error processing sheet data:');
      console.error('Message:', error.message);
      console.error('Stack:', error.stack);
      throw new Error(`Failed to process sheet data: ${error.message}`);
    }

  } catch (error) {
    console.error('FATAL ERROR in jobs-get:');
    console.error('Message:', error.message);
    console.error('Stack:', error.stack);
    
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({ 
        error: 'Failed to load jobs',
        message: error.message,
        details: process.env.NODE_ENV === 'development' ? error.stack : undefined
      }),
    };
  }
};