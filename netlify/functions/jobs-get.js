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
    console.log('Environment variables:', {
      hasServiceAccountEmail: !!process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL,
      hasPrivateKey: !!process.env.GOOGLE_PRIVATE_KEY,
      hasSpreadsheetId: !!process.env.GOOGLE_SPREADSHEET_ID,
    });

    if (!process.env.GOOGLE_SERVICE_ACCOUNT_EMAIL || 
        !process.env.GOOGLE_PRIVATE_KEY || 
        !process.env.GOOGLE_SPREADSHEET_ID) {
      throw new Error('Missing required environment variables');
    }

    // Google Sheets API setup
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
      console.log(`Loaded spreadsheet: ${doc.title}`);
    } catch (error) {
      console.error('Error loading spreadsheet:', error.message);
      throw new Error(`Failed to load spreadsheet: ${error.message}`);
    }

    const sheet = doc.sheetsByIndex[0]; // First sheet
    console.log(`Using sheet: ${sheet.title}`);

    try {
      console.log('Fetching rows...');
      await sheet.loadHeaderRow(); // Make sure to load header row first
      const rows = await sheet.getRows();
      console.log(`Fetched ${rows.length} rows from Google Sheets`);

      // Log headers for debugging
      console.log('Sheet headers:', sheet.headerValues);

      // Convert to job objects
      const jobs = rows.map((row, index) => {
        const job = {};
        const headers = sheet.headerValues || [];
        
        // Map headers to row data using row.get()
        headers.forEach((header) => {
          job[header] = row.get(header) || '';
        });

        console.log(`Processed job ${index + 1}:`, job);

        return {
          id: index + 1,
          title: job.title || '',
          description: job.description || '',
          type: job.type || '',
          deadline: job.deadline || '',
          created_at: job.created_at || new Date().toISOString()
        };
      });

      console.log('Successfully processed jobs:', jobs.length);
      return { 
        statusCode: 200, 
        headers,
        body: JSON.stringify({ jobs }) 
      };

    } catch (error) {
      console.error('Error processing sheet data:', error);
      throw new Error(`Failed to process sheet data: ${error.message}`);
    }

  } catch (error) {
    console.error('Error in jobs-get:', {
      message: error.message,
      stack: error.stack,
      name: error.name
    });
    
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