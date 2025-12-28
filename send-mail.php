<?php
/**
 * Trumarx Consultation Form - Email Handler
 * Uses cPanel to send form submissions
 */

// Error handling - must be first
error_reporting(0);
ini_set('display_errors', 0);

// CORS Headers - Must be sent before any output
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// For GET requests, show a simple status (useful for testing)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status' => 'ok', 'message' => 'Email endpoint is working. Use POST to send emails.']);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if JSON parsing failed
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
if (empty($data['name']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    exit();
}

// Sanitize input
$name = htmlspecialchars(strip_tags(trim($data['name'])));
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(strip_tags(trim($data['phone'] ?? 'Not provided')));
$subject = htmlspecialchars(strip_tags(trim($data['subject'] ?? 'Consultation Request')));
$message = htmlspecialchars(strip_tags(trim($data['message'] ?? 'No message provided')));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Email settings
$to = 'consultation@trumarx.in';
$email_subject = "New Consultation Request: $subject";

// Create HTML email body
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #D4AF37; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #1a365d; }
        .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #D4AF37; }
        .footer { padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>New Consultation Request</h1>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>$name</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'><a href='mailto:$email'>$email</a></div>
            </div>
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>$phone</div>
            </div>
            <div class='field'>
                <div class='label'>Subject:</div>
                <div class='value'>$subject</div>
            </div>
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>$message</div>
            </div>
        </div>
        <div class='footer'>
            This email was sent from the Trumarx website consultation form.
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Trumarx Website <consultation@trumarx.in>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Send email
$mail_sent = @mail($to, $email_subject, $email_body, $headers);

if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Your consultation request has been sent successfully!'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send email. Please try again later.'
    ]);
}
?>
