<?php
// Consultation Form Handler for cPanel with Anti-Spam Measures
// Uses PHP mail() function - works natively with cPanel email
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Anti-spam: Check submission time (honeypot timing)
    $submit_time = isset($_POST['submit_time']) ? (int)$_POST['submit_time'] : 0;
    $current_time = time();
    if ($submit_time > 0 && ($current_time - $submit_time) < 3) {
        // Submitted too fast - likely a bot
        echo json_encode(['success' => false, 'message' => 'Please wait a moment before submitting.']);
        exit;
    }
    
    // Anti-spam: Honeypot field (should be empty)
    $honeypot = isset($_POST['website']) ? trim($_POST['website']) : '';
    if (!empty($honeypot)) {
        // Bot filled the honeypot field
        echo json_encode(['success' => true, 'message' => 'Thank you for your submission.']); // Fake success
        exit;
    }
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'Consultation Request';
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
    
    // Anti-spam: Check message length
    if (strlen($message) < 10) {
        echo json_encode(['success' => false, 'message' => 'Message is too short. Please provide more details.']);
        exit;
    }
    
    if (strlen($message) > 5000) {
        echo json_encode(['success' => false, 'message' => 'Message is too long. Please keep it under 5000 characters.']);
        exit;
    }
    
    // Anti-spam: Check for suspicious patterns
    $spam_patterns = [
        '/\b(viagra|cialis|casino|poker|lottery|prize|winner)\b/i',
        '/\b(click here|buy now|limited time|act now)\b/i',
        '/(http:\/\/|https:\/\/|www\.)[^\s]{3,}/i' // Multiple URLs
    ];
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $message)) {
            // Suspicious content detected
            echo json_encode(['success' => true, 'message' => 'Thank you for your submission.']); // Fake success
            exit;
        }
    }
    
    // Email configuration
    $to = "molletitarkiksaiii@gmail.com, support@trumarx.in";
    $from = "mailservice@trumarx.in";
    $from_name = "Trumarx Consultation Portal";
    $email_subject = "New Consultation Request: " . $subject;
    
    // Build HTML email body with anti-spam headers
    $html_body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
                line-height: 1.6; 
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container { 
                max-width: 600px; 
                margin: 20px auto; 
                background: white;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .header { 
                background: linear-gradient(135deg, #1d1d1f 0%, #2d2d2f 100%);
                color: white; 
                padding: 30px 20px; 
                text-align: center;
            }
            .header h2 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }
            .subject-badge {
                background: rgba(255,255,255,0.1);
                padding: 8px 15px;
                border-radius: 5px;
                margin-top: 10px;
                font-size: 14px;
                display: inline-block;
            }
            .content { 
                padding: 30px 20px;
            }
            .field { 
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }
            .field:last-child {
                border-bottom: none;
            }
            .label { 
                font-weight: 600;
                color: #1d1d1f;
                display: block;
                margin-bottom: 5px;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .value {
                color: #555;
                font-size: 15px;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
            .footer { 
                text-align: center; 
                padding: 20px; 
                font-size: 12px; 
                color: #999;
                background: #f9f9f9;
                border-top: 1px solid #eee;
            }
            a {
                color: #0066cc;
                text-decoration: none;
            }
            .reply-button {
                display: inline-block;
                padding: 12px 24px;
                background: #1d1d1f;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Consultation Request</h2>
                <div class='subject-badge'>{$subject}</div>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Client Name</span>
                    <div class='value'>{$name}</div>
                </div>
                <div class='field'>
                    <span class='label'>Email Address</span>
                    <div class='value'>
                        <a href='mailto:{$email}' class='reply-button'>Reply to {$email}</a>
                    </div>
                </div>
                <div class='field'>
                    <span class='label'>Phone Number</span>
                    <div class='value'>" . ($phone ?: 'Not provided') . "</div>
                </div>
                <div class='field'>
                    <span class='label'>Message</span>
                    <div class='value'>" . nl2br($message) . "</div>
                </div>
                <div class='field'>
                    <span class='label'>Submitted On</span>
                    <div class='value'>" . date('F j, Y \a\t g:i A T') . "</div>
                </div>
                <div class='field'>
                    <span class='label'>IP Address</span>
                    <div class='value'>" . $_SERVER['REMOTE_ADDR'] . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from the Trumarx website consultation form.</p>
                <p>&copy; " . date('Y') . " Trumarx IP Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
    
    // Plain text version
    $plain_body = "New Consultation Request\n\n";
    $plain_body .= "Subject: {$subject}\n";
    $plain_body .= "Client Name: {$name}\n";
    $plain_body .= "Email: {$email}\n";
    $plain_body .= "Phone: " . ($phone ?: 'Not provided') . "\n";
    $plain_body .= "Message:\n{$message}\n\n";
    $plain_body .= "Submitted: " . date('F j, Y \a\t g:i A T') . "\n";
    $plain_body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    
    // Email headers with anti-spam measures
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/html; charset=UTF-8";
    $headers[] = "From: " . $from_name . " <" . $from . ">";
    $headers[] = "Reply-To: " . $name . " <" . $email . ">";
    $headers[] = "Return-Path: " . $from;
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "X-Priority: 3"; // Normal priority
    $headers[] = "X-MSMail-Priority: Normal";
    $headers[] = "Importance: Normal";
    
    // Anti-spam headers
    $headers[] = "X-Spam-Status: No"; // Declare not spam
    $headers[] = "X-Originating-IP: " . $_SERVER['REMOTE_ADDR'];
    $headers[] = "Message-ID: <" . time() . "." . md5($email . $name) . "@trumarx.in>";
    
    $headers_string = implode("\r\n", $headers);
    
    // Send email using PHP mail() function
    if (mail($to, $email_subject, $html_body, $headers_string)) {
        // Log successful submission (optional)
        $log_entry = date('Y-m-d H:i:s') . " - Consultation from: {$name} ({$email})\n";
        @file_put_contents('consultation_log.txt', $log_entry, FILE_APPEND);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your consultation request has been sent successfully. We will get back to you soon.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send your request. Please try again or email us directly at support@trumarx.in'
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
