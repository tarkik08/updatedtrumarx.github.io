<?php
require_once '../db_config.php';

$username = 'admin';
$new_password = 'password123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE admin_users SET password = :password WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':username', $username);
    
    if($stmt->execute()){
        echo "<h1>Success!</h1>";
        echo "<p>Password for user '<strong>$username</strong>' has been reset to: <strong>$new_password</strong></p>";
        echo "<p><a href='login.php'>Go to Login</a></p>";
        echo "<br><p style='color:red;'>IMPORTANT: Delete this file (reset_password.php) from your server after use for security.</p>";
    } else {
        echo "Error updating record.";
    }
} catch(PDOException $e){
    die("ERROR: Could not execute query. " . $e->getMessage());
}
?>
