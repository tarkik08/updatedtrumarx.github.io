<?php
// Job Application Handler with PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $experience = isset($_POST['experience']) ? htmlspecialchars(trim($_POST['experience'])) : '';
    $job_title = isset($_POST['job_title']) ? htmlspecialchars(trim($_POST['job_title'])) : '';
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
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'molletitarkiksai@gmail.com'; // Your Gmail
        $mail->Password = 'vhdq nrus zcbv mble'; // REPLACE WITH YOUR 16-CHAR APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email Settings
        $mail->setFrom('molletitarkiksai@gmail.com', 'Trumarx Career Portal');
        $mail->addAddress('molletitarkiksaiii@gmail.com');
        $mail->addAddress('career@trumarx.in');
        $mail->addReplyTo($email, $name);
        
        $mail->isHTML(false); // Plain text for better deliverability
        $mail->Subject = 'Job Application - ' . $job_title . ' - ' . $name;
        
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
        
        $mail->Body = $body;
        
        $mail->send();
        echo json_encode([
            'success' => true, 
            'message' => 'Your job application has been submitted successfully!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send application: ' . $mail->ErrorInfo
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
