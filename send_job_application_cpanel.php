<?php
// Job Application Handler - SPAM-FILTER OPTIMIZED
// Uses plain text format to avoid spam filters
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $experience = isset($_POST['experience']) ? htmlspecialchars(trim($_POST['experience'])) : '';
    $job_title = isset($_POST['job_title']) ? htmlspecialchars(trim($_POST['job_title'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
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
    $subject = "Job Application - " . $job_title . " - " . $name;
    
    // PLAIN TEXT email body (spam-filter friendly)
    $body = "JOB APPLICATION\n";
    $body .= str_repeat("=", 50) . "\n\n";
    
    $body .= "POSITION: " . $job_title . "\n\n";
    
    $body .= "APPLICANT INFORMATION\n";
    $body .= str_repeat("-", 50) . "\n";
    $body .= "Name: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Phone: " . ($phone ?: 'Not provided') . "\n";
    $body .= "Experience: " . ($experience ?: 'Not provided') . " years\n\n";
    
    $body .= "COVER LETTER\n";
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
    if (mail($to, $subject, $body, $headers_string)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your job application has been submitted successfully!'
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
