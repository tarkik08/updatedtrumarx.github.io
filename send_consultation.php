<?php
// Consultation Form Handler with SMTP
// Sends emails using SMTP for better deliverability

header('Content-Type: application/json');

// Include SMTP mailer
require_once 'smtp_mailer.php';

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
    $email_subject = "New Consultation Request: " . $subject;
    
    // Build email content
    $content = "
        <div class='field'>
            <span class='label'>Client Name</span>
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
            <span class='label'>Subject</span>
            <div class='value'>{$subject}</div>
        </div>
        <div class='field'>
            <span class='label'>Message</span>
            <div class='value'>" . nl2br($message) . "</div>
        </div>
        <div class='field'>
            <span class='label'>Submitted On</span>
            <div class='value'>" . date('F j, Y \a\t g:i A') . "</div>
        </div>
    ";
    
    // Create plain text version
    $plain_text = "New Consultation Request\n\n";
    $plain_text .= "Client Name: {$name}\n";
    $plain_text .= "Email: {$email}\n";
    $plain_text .= "Phone: " . ($phone ?: 'Not provided') . "\n";
    $plain_text .= "Subject: {$subject}\n";
    $plain_text .= "Message: {$message}\n";
    $plain_text .= "Submitted: " . date('F j, Y \a\t g:i A') . "\n";
    
    // Initialize SMTP mailer
    $mailer = new SMTPMailer();
    
    // Send email (plain text only)
    if ($mailer->send($to, $email_subject, '', $plain_text, $email)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your consultation request has been sent successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send email. Please try again or contact us directly.',
            'error' => $mailer->getLastError()
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
