<?php
// Consultation Form Handler
// Sends emails from mailservice@trumarx.in to support@trumarx.in

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'Consultation Request';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validate required fields
    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Email configuration
    $to = "molletitarkiksaiii@gmail.com, support@trumarx.in";
    $from = "mailservice@trumarx.in";
    $email_subject = "Consultation Request: " . $subject;
    
    // Build email body
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #0a1628; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #0a1628; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Consultation Request</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span> " . $name . "
                </div>
                <div class='field'>
                    <span class='label'>Email:</span> " . $email . "
                </div>
                <div class='field'>
                    <span class='label'>Phone:</span> " . ($phone ?: 'Not provided') . "
                </div>
                <div class='field'>
                    <span class='label'>Subject:</span> " . $subject . "
                </div>
                <div class='field'>
                    <span class='label'>Message:</span><br>" . nl2br($message) . "
                </div>
                <div class='field'>
                    <span class='label'>Submitted:</span> " . date('Y-m-d H:i:s') . "
                </div>
            </div>
            <div class='footer'>
                This email was sent from the Trumarx consultation form.
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Trumarx Consultation <" . $from . ">" . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $email_subject, $email_body, $headers)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your consultation request has been sent successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send email. Please try again or contact us directly.'
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
