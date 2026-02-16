<?php
// Consultation Handler with PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prevent PHP warnings from breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

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
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'General Inquiry';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and message are required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    }
    
    // ==========================================
    // DATABASE STORAGE
    // ==========================================
    try {
        require_once 'db_config.php';
        
        $stmt = $pdo->prepare("INSERT INTO consultations (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        
    } catch (Exception $e) {
        // Log database error but don't stop email sending
        error_log("Database Error (Consultation): " . $e->getMessage());
    }
    
    try {
        $mail = new PHPMailer(true);
        // ... rest of email logic ...

        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // CREDENTIALS - DO NOT CHANGE
        $mail->Username = 'trumarxmailservice@gmail.com'; 
        $mail->Password = 'wsqeqpygjqawewlb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email Settings
        $mail->setFrom('trumarxmailservice@gmail.com', 'Trumarx Consultation Portal');
        $mail->addAddress('molletitarkiksaiii@gmail.com');
        $mail->addAddress('support@trumarx.in');
        $mail->addReplyTo($email, $name);
        
        $mail->isHTML(true); // HTML Enabled
        $mail->Subject = 'Consultation Request - ' . $subject;
        
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
                .header .subtitle { font-size: 14px; color: #d4af37; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; }
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
                    <h1>Consultation Request</h1>
                    <div class='subtitle'>" . $subject . "</div>
                </div>
                <div class='content'>
                    <div class='section-title'>Client Details</div>
                    
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
                    
                    <div class='section-title' style='margin-top: 30px;'>Message</div>
                    <div class='message-box'>
                        <div class='value'>" . nl2br($message) . "</div>
                    </div>

                    <div style='text-align: center; margin-top: 30px;'>
                         <a href='mailto:" . $email . "' class='btn'>Reply to Client</a>
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
        $mail->AltBody = "Consultation Request: $subject. From: $name ($email). Message: $message";
        
        $mail->send();
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your consultation request has been sent successfully.'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send your request: ' . $mail->ErrorInfo
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
