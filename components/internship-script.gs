// Internship Application Script

function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    var doc = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = doc.getSheetByName('Internship Applications'); // Ensure this sheet exists!

    if (!sheet) {
      sheet = doc.insertSheet('Internship Applications');
      // Set headers if new sheet
      sheet.appendRow(['Timestamp', 'First Name', 'Last Name', 'Email', 'Phone', 'Experience', 'Institution', 'Start Date', 'End Date', 'Message', 'Page', 'File Name', 'File Size']);
    }

    var headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
    var nextRow = sheet.getLastRow() + 1;

    var newRow = headers.map(function(header) {
      if (header === 'Timestamp') return new Date();
      
      // Map headers to parameters
      // Note: These keys must match the `metadataForSheets` keys in your JS
      switch(header) {
        case 'First Name': return e.parameter.firstName;
        case 'Last Name': return e.parameter.lastName;
        case 'Email': return e.parameter.email;
        case 'Phone': return e.parameter.phone;
        case 'Experience': return e.parameter.experience;
        case 'Institution': return e.parameter.institution;
        case 'Start Date': return e.parameter.startDate;
        case 'End Date': return e.parameter.endDate;
        case 'Message': return e.parameter.message;
        case 'Page': return e.parameter.page;
        case 'File Name': return e.parameter.fileName;
        case 'File Size': return e.parameter.fileSize;
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
