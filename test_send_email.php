<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$to = "molletitarkiksaiii@gmail.com";
$subject = "Test Email from testing.trumarx.in - " . date('H:i:s');
$message = "This is a test email sent at " . date('Y-m-d H:i:s') . "\n\n";
$message .= "If you receive this, cPanel email is working correctly!";

$headers = "From: Trumarx Test <mailservice@trumarx.in>\r\n";
$headers .= "Reply-To: mailservice@trumarx.in\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$result = @mail($to, $subject, $message, $headers);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Email sent successfully! Check your inbox and spam folder.',
        'to' => $to,
        'time' => date('Y-m-d H:i:s')
    ]);
} else {
    $error = error_get_last();
    echo json_encode([
        'success' => false,
        'message' => 'Email failed to send',
        'error' => $error ? $error['message'] : 'Unknown error',
        'mail_function_exists' => function_exists('mail'),
        'sendmail_path' => ini_get('sendmail_path')
    ]);
}
?>
