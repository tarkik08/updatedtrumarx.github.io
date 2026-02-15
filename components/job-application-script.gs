// Job Application Script

function doPost(e) {
  try {
    const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    
    // Get data from POST request
    let data = e.parameter;
    
    // Fallback: try parsing JSON if parameter is empty
    if (!data || Object.keys(data).length === 0) {
      if (e.postData && e.postData.contents) {
        try {
          data = JSON.parse(e.postData.contents);
        } catch (err) {
          Logger.log('Error parsing JSON: ' + err);
          data = {};
        }
      }
    }
    
    // Log received data for debugging
    Logger.log('Received data: ' + JSON.stringify(data));
    
    // Append row to sheet - Make sure columns match this order
    // Order: Timestamp | Name | Email | Phone | Experience | Job Title | Message | File Name | File Size | Drive File URL | Page
    sheet.appendRow([
      data.timestamp || new Date().toISOString(),
      data.name || '',
      data.email || '',
      data.phone || '',
      data.experience || '',
      data.job_title || '',
      data.message || '',
      data.fileName || '',
      data.fileSize || '',
      data.driveFileUrl || 'N/A',
      data.page || ''
    ]);
    
    // Return success response
    return ContentService.createTextOutput(JSON.stringify({
      status: 'success',
      message: 'Data saved successfully'
    })).setMimeType(ContentService.MimeType.JSON);
    
  } catch (error) {
    // Log error
    Logger.log('Error: ' + error.toString());
    
    // Return error response
    return ContentService.createTextOutput(JSON.stringify({
      status: 'error',
      message: error.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}
