<?php
session_start();
require_once '../../config/database.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_csv'])) {
    $file = $_FILES['file_csv'];
    
    // Validate file type
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'csv') {
        header("Location: ../../dashboard/index.php?page=teachers&error=Invalid file format. Please upload CSV.");
        exit();
    }

    $handle = fopen($file['tmp_name'], "r");
    if ($handle === FALSE) {
        header("Location: ../../dashboard/index.php?page=teachers&error=Cannot open file.");
        exit();
    }

    $row = 0;
    $success_count = 0;
    $errors = [];

    // Parse CSV
    // Expected Columns: full_name, nip, gender, status, phone, address, email, username, password
    
    try {
        $pdo->beginTransaction();

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Skip header

            // Basic Validation
            if (count($data) < 9) continue; // Not enough columns

            $full_name = htmlspecialchars(trim($data[0]));
            $nip = htmlspecialchars(trim($data[1]));
            $gender = strtoupper(trim($data[2])); // L or P
            $status = htmlspecialchars(trim($data[3])); // PNS, Honorer, etc
            $phone = htmlspecialchars(trim($data[4]));
            $address = htmlspecialchars(trim($data[5]));
            $email = htmlspecialchars(trim($data[6]));
            $username = htmlspecialchars(trim($data[7]));
            $password = trim($data[8]);

            // Insert Teacher
            $stmt = $pdo->prepare("INSERT INTO teachers (full_name, nip, gender, status, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$full_name, $nip, $gender, $status, $phone, $address]);
                $teacher_id = $pdo->lastInsertId();

                // Insert User Login
                if (!empty($username) && !empty($password)) {
                    $u_pass_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_u = $pdo->prepare("INSERT INTO users (username, email, password, role, related_id, is_active) VALUES (?, ?, ?, 'teacher', ?, 1)");
                    $stmt_u->execute([$username, $email, $u_pass_hash, $teacher_id]);
                }
                $success_count++;

            } catch (PDOException $e) {
                // Log error but continue (or break depending on req, strictly here we catch dup entries)
            }
        }

        $pdo->commit();
        fclose($handle);
        header("Location: ../../dashboard/index.php?page=teachers&status=success&msg=Imported $success_count teachers successfully.");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        fclose($handle);
        header("Location: ../../dashboard/index.php?page=teachers&error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../dashboard/index.php?page=teachers");
    exit();
}
?>
