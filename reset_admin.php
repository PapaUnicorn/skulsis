<?php
require_once 'config/database.php';

echo "Resetting Admin Password...\n";

$username = 'admin';
$password = 'admin123';
$email = 'admin@skulsis.com';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing
        $sql = "UPDATE users SET password = ?, is_active = 1 WHERE username = ?";
        $stmt_upd = $pdo->prepare($sql);
        $stmt_upd->execute([$hash, $username]);
        echo "SUCCESS: Password for user '$username' has been reset to '$password'.\n";
    } else {
        // Insert new
        $sql = "INSERT INTO users (username, email, password, role, is_active) VALUES (?, ?, ?, 'admin', 1)";
        $stmt_ins = $pdo->prepare($sql);
        $stmt_ins->execute([$username, $email, $hash]);
        echo "SUCCESS: User '$username' created with password '$password'.\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
