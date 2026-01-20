<?php
// Simple PHP Mail Test Script
// Upload this to your cPanel /public_html directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = filter_var($_POST['recipient'], FILTER_SANITIZE_EMAIL);
    $from = filter_var($_POST['sender'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $subject = "Test Email from cPanel Server";
    $message = "
    <html>
    <head>
    <title>Test Email</title>
    </head>
    <body>
    <h2>It Works!</h2>
    <p>This email confirms that your cPanel server is correctly configured to send emails via PHP.</p>
    <p><strong>Sender:</strong> $from</p>
    <p><strong>Recipient:</strong> $to</p>
    <p><strong>Time:</strong> " . date("Y-m-d H:i:s") . "</p>
    </body>
    </html>
    ";

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <" . $from . ">" . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Check if mail is sent
    echo '<div style="max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; font-family:sans-serif; border-radius:10px;">';
    
    if(mail($to, $subject, $message, $headers)) {
        echo '<h2 style="color:green;">✅ Success!</h2>';
        echo '<p>PHP mail() function executed successfully.</p>';
        echo '<p>Please check your inbox (and SPAM folder) at <strong>' . $to . '</strong>.</p>';
    } else {
        echo '<h2 style="color:red;">❌ Failed</h2>';
        echo '<p>The PHP mail() function failed to execute.</p>'; 
        echo '<p>Possible reasons:</p><ul>';
        echo '<li>Your server provider has blocked the mail() function.</li>';
        echo '<li>Incorrect sender email address (must verify domain ownership).</li>';
        echo '<li>Server misconfiguration.</li></ul>';
    }
    
    echo '<br><a href="test_email.html" style="text-decoration:none; background:#eee; padding:10px 20px; border-radius:5px; color:#333;">Try Again</a>';
    echo '</div>';

} else {
    echo "Method not allowed.";
}
?>
