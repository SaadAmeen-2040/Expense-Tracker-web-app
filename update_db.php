<?php
require_once 'api/db.php';

try {
    echo "<h1>Database Update</h1>";
    
    // 1. Add email column if not exists
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) AFTER username");
    echo "<p>Email column added/verified.</p>";
    
    // 2. Update admin credentials
    $username = 'admin';
    $email = 'admin@expense.com';
    $newPassword = 'Admin@1234';
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE username = ?");
    $stmt->execute([$email, $hash, $username]);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>Admin credentials updated successfully!</p>";
        echo "<ul><li>Email: $email</li><li>Password: $newPassword</li></ul>";
    } else {
        echo "<p style='color:orange;'>Admin user not found or no changes made.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>
