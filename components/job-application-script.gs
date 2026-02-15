// Job Application Script

function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    var doc = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = doc.getSheetByName('Job Applications'); // Ensure this sheet exists!

    if (!sheet) {
      sheet = doc.insertSheet('Job Applications');
      // Set headers if new sheet
      sheet.appendRow(['Timestamp', 'Name', 'Email', 'Phone', 'Experience', 'Job Title', 'Message', 'Page', 'File Name', 'File Size', 'Drive File URL']);
    }

    var headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
    var nextRow = sheet.getLastRow() + 1;

    var newRow = headers.map(function(header) {
      if (header === 'Timestamp') return new Date();
      
      // Map headers to parameters
      // Note: These keys must match the `metadataForSheets` keys in your JS
      switch(header) {
        case 'Name': return e.parameter.name;
        case 'Email': return e.parameter.email;
        case 'Phone': return e.parameter.phone;
        case 'Experience': return e.parameter.experience;
        case 'Job Title': return e.parameter.job_title;
        case 'Message': return e.parameter.message;
        case 'Page': return e.parameter.page;
        case 'File Name': return e.parameter.fileName;
        case 'File Size': return e.parameter.fileSize;
        case 'Drive File URL': return e.parameter.driveFileUrl || 'N/A'; // Will rely on PHP return if integrated later
        default: return '';
      }
    });

    sheet.getRange(nextRow, 1, 1, newRow.length).setValues([newRow]);

    return ContentService
      .createTextOutput(JSON.stringify({ 'result': 'success', 'row': nextRow }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (e) {
    return ContentService
      .createTextOutput(JSON.stringify({ 'result': 'error', 'error': e }))
      .setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}
