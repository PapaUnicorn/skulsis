<?php
session_start();
require_once '../../config/database.php';

// Auth Check (Basic) - Ensure only Admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;
    $full_name = htmlspecialchars($_POST['full_name']);
    $nisn = htmlspecialchars($_POST['nisn']);
    $nis = htmlspecialchars($_POST['nis']);
    $gender = $_POST['gender'];
    $birth_place = htmlspecialchars($_POST['birth_place']);
    $birth_date = $_POST['birth_date'];
    $parent_phone = htmlspecialchars($_POST['parent_phone']);
    $address = htmlspecialchars($_POST['address']);

    try {
        $pdo->beginTransaction();

        if ($id) {
            // Update
            $sql = "UPDATE students SET full_name=?, nisn=?, nis=?, gender=?, birth_place=?, birth_date=?, parent_phone=?, address=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $nisn, $nis, $gender, $birth_place, $birth_date, $parent_phone, $address, $id]);
        } else {
            // Insert Student
            $sql = "INSERT INTO students (full_name, nisn, nis, gender, birth_place, birth_date, parent_phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $nisn, $nis, $gender, $birth_place, $birth_date, $parent_phone, $address]);
            $new_student_id = $pdo->lastInsertId();

            // Insert User if fields exist
            if (!empty($_POST['user_username']) && !empty($_POST['user_password']) && !empty($_POST['user_email'])) {
                $u_username = htmlspecialchars($_POST['user_username']);
                $u_email = htmlspecialchars($_POST['user_email']);
                $u_password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
                
                $sql_user = "INSERT INTO users (username, email, password, role, related_id, is_active) VALUES (?, ?, ?, 'student', ?, 1)";
                $stmt_user = $pdo->prepare($sql_user);
                $stmt_user->execute([$u_username, $u_email, $u_password, $new_student_id]);
            }
        }
        
        $pdo->commit();
        header("Location: ../../dashboard/index.php?page=students&status=success");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Handle constraint violation
        header("Location: ../../dashboard/index.php?page=students&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=students");
    exit();
}
?>
