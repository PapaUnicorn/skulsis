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
        header("Location: ../../dashboard/index.php?page=students&error=Invalid file format. Please upload CSV.");
        exit();
    }

    $handle = fopen($file['tmp_name'], "r");
    if ($handle === FALSE) {
        header("Location: ../../dashboard/index.php?page=students&error=Cannot open file.");
        exit();
    }

    $row = 0;
    $success_count = 0;

    // Parse CSV
    // Expected Columns: full_name, nisn, nis, gender, birth_place, birth_date, parent_phone, address, email, username, password
    
    try {
        $pdo->beginTransaction();

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Skip header

            // Basic Validation
            if (count($data) < 11) continue; 

            $full_name = htmlspecialchars(trim($data[0]));
            $nisn = htmlspecialchars(trim($data[1]));
            $nis = htmlspecialchars(trim($data[2]));
            $gender = strtoupper(trim($data[3]));
            $birth_place = htmlspecialchars(trim($data[4]));
            $birth_date = trim($data[5]); // YYYY-MM-DD
            $parent_phone = htmlspecialchars(trim($data[6]));
            $address = htmlspecialchars(trim($data[7]));
            $email = htmlspecialchars(trim($data[8]));
            $username = htmlspecialchars(trim($data[9]));
            $password = trim($data[10]);

            // Insert Student
            $stmt = $pdo->prepare("INSERT INTO students (full_name, nisn, nis, gender, birth_place, birth_date, parent_phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$full_name, $nisn, $nis, $gender, $birth_place, $birth_date, $parent_phone, $address]);
                $student_id = $pdo->lastInsertId();

                // Insert User Login
                if (!empty($username) && !empty($password)) {
                    $u_pass_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_u = $pdo->prepare("INSERT INTO users (username, email, password, role, related_id, is_active) VALUES (?, ?, ?, 'student', ?, 1)");
                    $stmt_u->execute([$username, $email, $u_pass_hash, $student_id]);
                }
                $success_count++;

            } catch (PDOException $e) {
                // Log error
            }
        }

        $pdo->commit();
        fclose($handle);
        header("Location: ../../dashboard/index.php?page=students&status=success&msg=Imported $success_count students successfully.");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        fclose($handle);
        header("Location: ../../dashboard/index.php?page=students&error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../dashboard/index.php?page=students");
    exit();
}
?>
