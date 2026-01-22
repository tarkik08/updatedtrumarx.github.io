<?php
// Consultation Form Handler - SPAM-FILTER OPTIMIZED
// Uses plain text format to avoid spam filters
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'General Inquiry';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and message are required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Email configuration
    $to = "molletitarkiksaiii@gmail.com, support@trumarx.in";
    $from = "mailservice@trumarx.in";
    $from_name = "Trumarx Consultation Portal";
    $email_subject = "Consultation Request - " . $subject;
    
    // PLAIN TEXT email body (spam-filter friendly)
    $body = "CONSULTATION REQUEST\n";
    $body .= str_repeat("=", 50) . "\n\n";
    
    $body .= "SUBJECT: " . $subject . "\n\n";
    
    $body .= "CLIENT INFORMATION\n";
    $body .= str_repeat("-", 50) . "\n";
    $body .= "Name: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Phone: " . ($phone ?: 'Not provided') . "\n\n";
    
    $body .= "MESSAGE\n";
    $body .= str_repeat("-", 50) . "\n";
    $body .= $message . "\n\n";
    
    $body .= "SUBMISSION DETAILS\n";
    $body .= str_repeat("-", 50) . "\n";
    $body .= "Submitted: " . date('F j, Y \a\t g:i A T') . "\n";
    $body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
    
    $body .= str_repeat("=", 50) . "\n";
    $body .= "Trumarx IP Services\n";
    $body .= "No. 23, Hari Prem Complex, 2nd Floor\n";
    $body .= "CMH Road, Indiranagar 1st Stage\n";
    $body .= "Bangalore - 560038\n";
    $body .= "Email: support@trumarx.in\n";
    $body .= "Website: https://trumarx.in\n";
    $body .= str_repeat("=", 50) . "\n";
    
    // Spam-filter-friendly headers
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: 8bit";
    $headers[] = "From: " . $from_name . " <" . $from . ">";
    $headers[] = "Reply-To: " . $name . " <" . $email . ">";
    $headers[] = "Return-Path: " . $from;
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "X-Priority: 3";
    $headers[] = "Importance: Normal";
    $headers[] = "Message-ID: <" . time() . "." . md5($email) . "@trumarx.in>";
    $headers[] = "Date: " . date('r');
    
    $headers_string = implode("\r\n", $headers);
    
    // Send email
    if (mail($to, $email_subject, $body, $headers_string)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your consultation request has been sent successfully.'
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
