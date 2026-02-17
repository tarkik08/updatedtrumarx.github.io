<?php
require_once "../db_config.php";

echo "<h1>Database Update Script</h1>";

try {
    // 1. Add 'status' column to tables if not exists
    $tables = ['consultations', 'internships', 'job_applications'];
    
    foreach ($tables as $table) {
        try {
            // Check if column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'status'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `status` VARCHAR(20) DEFAULT 'pending'");
                echo "<p style='color: green;'>✅ Added 'status' column to <strong>$table</strong>.</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ 'status' column already exists in <strong>$table</strong>.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Error updating $table: " . $e->getMessage() . "</p>";
        }
    }

    // 2. Update Admin Password
    $new_password = 'Trumarx@2026!';
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Check if admin user exists, if so update, else insert
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $updateStmt = $pdo->prepare("UPDATE admin_users SET password = :password WHERE username = 'admin'");
        $updateStmt->execute([':password' => $hashed_password]);
        echo "<p style='color: green;'>✅ Admin password updated successfully.</p>";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES ('admin', :password)");
        $insertStmt->execute([':password' => $hashed_password]);
        echo "<p style='color: green;'>✅ Admin user created with new password.</p>";
    }

} catch (PDOException $e) {
    die("CRITICAL ERROR: " . $e->getMessage());
}

echo "<h3>🎉 Update Complete! You can now <a href='login.php'>Login</a></h3>";
?>
