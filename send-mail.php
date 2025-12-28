<?php
/**
 * Trumarx Consultation Form - Email Handler
 */

// CRITICAL: Send CORS headers first, before ANYTHING else
// Use @ to suppress any potential errors
@header('Access-Control-Allow-Origin: *');
@header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
@header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
@header('Content-Type: application/json; charset=UTF-8');

// Flush headers immediately
if (function_exists('header_remove')) {
    // Ensure no buffering
}
ob_start();

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit();
}

// Disable error display (prevents breaking JSON output)
error_reporting(0);
ini_set('display_errors', 0);

// For GET requests, show status (for testing)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status' => 'ok', 'message' => 'Email endpoint is working. Use POST to send emails.']);
    ob_end_flush();
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    ob_end_flush();
    exit();
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if JSON parsing failed
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    ob_end_flush();
    exit();
}

// Validate required fields
if (empty($data['name']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    ob_end_flush();
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
    ob_end_flush();
    exit();
}

// Email settings
$to = 'consultation@trumarx.in';
$email_subject = "New Consultation Request: $subject";

// Create plain text email body (more reliable than HTML)
$email_body = "
New Consultation Request from Trumarx Website
=============================================

Name: $name
Email: $email
Phone: $phone
Subject: $subject

Message:
$message

---
This email was sent from the Trumarx website consultation form.
";

// Simple email headers
$headers = "From: consultation@trumarx.in\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

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

ob_end_flush();
?>
