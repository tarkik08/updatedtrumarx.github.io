<?php
// Internship Application Handler for cPanel
// Uses PHP mail() function - works natively with cPanel email
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
    
    // Email configuration
    $to = "molletitarkiksaiii@gmail.com, career@trumarx.in";
    $from = "mailservice@trumarx.in";
    $from_name = "Trumarx Career Portal";
    $subject = "New Internship Application - " . $name;
    
    // Build HTML email body
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
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Internship Application</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Applicant Name</span>
                    <div class='value'>{$name}</div>
                </div>
                <div class='field'>
                    <span class='label'>Email Address</span>
                    <div class='value'><a href='mailto:{$email}'>{$email}</a></div>
                </div>
                <div class='field'>
                    <span class='label'>Phone Number</span>
                    <div class='value'>" . ($phone ?: 'Not provided') . "</div>
                </div>
                <div class='field'>
                    <span class='label'>University/College</span>
                    <div class='value'>" . ($university ?: 'Not provided') . "</div>
                </div>
                <div class='field'>
                    <span class='label'>Cover Letter</span>
                    <div class='value'>" . nl2br($message) . "</div>
                </div>
                <div class='field'>
                    <span class='label'>Submitted On</span>
                    <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from the Trumarx website internship application form.</p>
                <p>&copy; " . date('Y') . " Trumarx IP Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
    
    // Plain text version for email clients that don't support HTML
    $plain_body = "New Internship Application\n\n";
    $plain_body .= "Applicant Name: {$name}\n";
    $plain_body .= "Email: {$email}\n";
    $plain_body .= "Phone: " . ($phone ?: 'Not provided') . "\n";
    $plain_body .= "University/College: " . ($university ?: 'Not provided') . "\n";
    $plain_body .= "Cover Letter: {$message}\n";
    $plain_body .= "Submitted: " . date('F j, Y \a\t g:i A') . "\n";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $from_name . " <" . $from . ">" . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email using PHP mail() function (works with cPanel)
    if (mail($to, $subject, $html_body, $headers)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your internship application has been submitted successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send application. Please try again or email us directly at career@trumarx.in'
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
