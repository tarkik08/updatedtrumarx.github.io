<?php
// contact.php

// 1. Set Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust in production for security if needed
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit();
}

// 3. Get and Decode Input
$input = json_decode(file_get_contents('php://input'), true);

// Fallback to $_POST if JSON is empty (for standard form submits)
if (!$input) {
    $input = $_POST;
}

// 4. Validate Input
$name = filter_var($input['name'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = filter_var($input['phone'] ?? '', FILTER_SANITIZE_STRING);
$subject = filter_var($input['subject'] ?? 'New Contact Form Submission', FILTER_SANITIZE_STRING);
$message = filter_var($input['message'] ?? '', FILTER_SANITIZE_STRING);

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit();
}

// 5. Email Configuration
// =========================================================================
// TODO: CHANGE THIS TO YOUR EMAIL ADDRESS
$to = "molletitarkiksaiii@gmail.com"; // Replace with your actual email
// =========================================================================

$email_subject = "Contact Form: " . $subject;

$email_body = "You have received a new message from your website contact form.\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: $phone\n\n";
$email_body .= "Message:\n$message\n";

$headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// 6. Send Email
if (mail($to, $email_subject, $email_body, $headers)) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
}
?>
