function doPost(e) {
  // Lock to prevent concurrent issues (optional but good practice)
  var lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    var data = e.parameter;
    var fileData = data.file;
    var fileName = data.filename;
    var folderId = "YOUR_FOLDER_ID_HERE"; // REPLACE WITH YOUR DRIVE FOLDER ID

    // Decode file
    var contentType = data.mimeType || "application/pdf";
    var decoded = Utilities.base64Decode(fileData);
    var blob = Utilities.newBlob(decoded, contentType, fileName);

    // Get Folder
    var folder;
    if (folderId && folderId !== "YOUR_FOLDER_ID_HERE") {
      try {
        folder = DriveApp.getFolderById(folderId);
      } catch (err) {
        folder = DriveApp.getRootFolder();
      }
    } else {
      folder = DriveApp.getRootFolder();
    }

    // Create File
    var file = folder.createFile(blob);
    
    // Set public permission (optional - easier for viewing)
    file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);

    var fileUrl = file.getUrl();

    return ContentService
      .createTextOutput(JSON.stringify({ 
        "result": "success", 
        "url": fileUrl 
      }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (e) {
    return ContentService
      .createTextOutput(JSON.stringify({ 
        "result": "error", 
        "error": e.toString() 
      }))
      .setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}
