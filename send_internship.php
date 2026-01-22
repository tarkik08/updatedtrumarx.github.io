<?php
// Internship Application Handler with SMTP
header('Content-Type: application/json');

// Include SMTP mailer
require_once 'smtp_mailer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $university = isset($_POST['university']) ? htmlspecialchars(trim($_POST['university'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    $to = "molletitarkiksaiii@gmail.com, career@trumarx.in";
    $email_subject = "New Internship Application - " . $name;
    
    // Build email content
    $content = "
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
    ";
    
    // Create plain text version
    $plain_text = "New Internship Application\n\n";
    $plain_text .= "Applicant Name: {$name}\n";
    $plain_text .= "Email: {$email}\n";
    $plain_text .= "Phone: " . ($phone ?: 'Not provided') . "\n";
    $plain_text .= "University/College: " . ($university ?: 'Not provided') . "\n";
    $plain_text .= "Cover Letter: {$message}\n";
    $plain_text .= "Submitted: " . date('F j, Y \a\t g:i A') . "\n";
    
    // Initialize SMTP mailer
    $mailer = new SMTPMailer();
    
    // Send email (plain text only)
    if ($mailer->send($to, $email_subject, '', $plain_text, $email)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your internship application has been submitted successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send application. Please try again or contact us directly.',
            'error' => $mailer->getLastError()
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
