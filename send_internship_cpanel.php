<?php
// Internship Application Handler with PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $university = isset($_POST['university']) ? htmlspecialchars(trim($_POST['university'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validation
    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Handle file upload
    $uploadedFile = null;
    $fileName = '';
    
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cv'];
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        
        // Validate file size (10MB max)
        if ($fileSize > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 10MB.']);
            exit;
        }
        
        // Validate file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Only PDF, DOC, and DOCX files are allowed.']);
            exit;
        }
        
        $uploadedFile = $fileTmpPath;
    } else if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle upload errors
        $errorMessage = 'File upload error: ';
        switch ($_FILES['cv']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage .= 'File is too large.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage .= 'File was only partially uploaded.';
                break;
            default:
                $errorMessage .= 'Unknown error occurred.';
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'CV/Resume is required.']);
        exit;
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // CREDENTIALS - DO NOT CHANGE
        $mail->Username = 'trumarxmailservice@gmail.com'; 
        $mail->Password = 'wsqe qpyg jqaw ewlb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email Settings
        $mail->setFrom('trumarxmailservice@gmail.com', 'Trumarx Career Portal');
        $mail->addAddress('molletitarkiksaiii@gmail.com');
        $mail->addAddress('career@trumarx.in');
        $mail->addReplyTo($email, $name);
        
        $mail->isHTML(true); // HTML Enabled
        $mail->Subject = 'Internship Application - ' . $name;
        
        // HTML Email Template
        $html_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; color: #333333; }
                .email-container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background-color: #1d1d1f; color: #ffffff; padding: 30px 20px; text-align: center; border-bottom: 3px solid #d4af37; }
                .header h1 { margin: 0; font-size: 24px; font-weight: 600; letter-spacing: 1px; }
                .content { padding: 40px 30px; }
                .section-title { font-size: 14px; color: #d4af37; font-weight: 700; text-transform: uppercase; margin-bottom: 10px; border-bottom: 2px solid #f0f0f0; padding-bottom: 5px; letter-spacing: 0.5px; }
                .data-row { margin-bottom: 20px; }
                .label { font-size: 13px; color: #666666; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 5px; }
                .value { font-size: 16px; color: #1d1d1f; line-height: 1.5; font-weight: 500; }
                .message-box { background-color: #f9f9f9; border-left: 4px solid #1d1d1f; padding: 15px; margin-top: 5px; border-radius: 4px; }
                .footer { background-color: #1d1d1f; color: #888888; padding: 30px; text-align: center; font-size: 13px; line-height: 1.6; border-top: 1px solid #333; }
                .footer p { margin: 5px 0; }
                .footer strong { color: #ffffff; }
                .footer a { color: #d4af37; text-decoration: none; }
                .btn { display: inline-block; background-color: #1d1d1f; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-weight: 600; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>Internship Application</h1>
                </div>
                <div class='content'>
                    <div class='section-title'>Applicant Details</div>
                    
                    <div class='data-row'>
                        <span class='label'>Full Name</span>
                        <div class='value'>" . $name . "</div>
                    </div>
                    
                    <div class='data-row'>
                        <span class='label'>Email Address</span>
                        <div class='value'><a href='mailto:" . $email . "' style='color: #1d1d1f; text-decoration: none;'>" . $email . "</a></div>
                    </div>
                    
                    <div class='data-row'>
                        <span class='label'>Phone Number</span>
                        <div class='value'>" . ($phone ?: 'Not provided') . "</div>
                    </div>
                    
                    <div class='data-row'>
                        <span class='label'>University / College</span>
                        <div class='value'>" . ($university ?: 'Not provided') . "</div>
                    </div>
                    
                    <div class='section-title' style='margin-top: 30px;'>Cover Letter</div>
                    <div class='message-box'>
                        <div class='value'>" . nl2br($message) . "</div>
                    </div>

                    <div style='text-align: center; margin-top: 30px;'>
                         <a href='mailto:" . $email . "' class='btn'>Reply to Applicant</a>
                    </div>
                    
                    <div style='margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; font-size: 12px; color: #999; text-align: center;'>
                        Submitted On: " . date('F j, Y \a\t g:i A T') . "<br>
                        IP Address: " . $_SERVER['REMOTE_ADDR'] . "
                    </div>
                </div>
                <div class='footer'>
                    <p><strong>Trumarx IP Services</strong></p>
                    <p>No. 23, Hari Prem Complex, 2nd Floor</p>
                    <p>CMH Road, Indiranagar 1st Stage</p>
                    <p>Bangalore - 560038</p>
                    <p style='margin-top: 15px;'>
                        <a href='mailto:support@trumarx.in'>support@trumarx.in</a> &bull; 
                        <a href='https://trumarx.in'>trumarx.in</a>
                    </p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $html_body;
        // Plain text fallback
        $mail->AltBody = "Internship Application from $name. Email: $email. Message: $message";
        
        // Attach CV/Resume if uploaded
        if ($uploadedFile && file_exists($uploadedFile)) {
            $mail->addAttachment($uploadedFile, $fileName);
        }
        
        $mail->send();
        echo json_encode([
            'success' => true, 
            'message' => 'Your internship application has been submitted successfully!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send application: ' . $mail->ErrorInfo
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
