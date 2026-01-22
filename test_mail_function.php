<?php
header('Content-Type: application/json');

if (function_exists('mail')) {
    echo json_encode([
        'success' => true,
        'message' => 'mail() function is available',
        'sendmail_path' => ini_get('sendmail_path'),
        'php_version' => phpversion()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'mail() function is DISABLED on this server'
    ]);
}
?>
