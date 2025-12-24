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
    $nip = htmlspecialchars($_POST['nip']);
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    try {
        $pdo->beginTransaction();

        if ($id) {
            // Update
            $sql = "UPDATE teachers SET full_name=?, nip=?, gender=?, status=?, phone=?, address=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $nip, $gender, $status, $phone, $address, $id]);
        } else {
            // Insert Teacher
            $sql = "INSERT INTO teachers (full_name, nip, gender, status, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $nip, $gender, $status, $phone, $address]);
            $new_teacher_id = $pdo->lastInsertId();

            // Insert User if fields exist
            if (!empty($_POST['user_username']) && !empty($_POST['user_password']) && !empty($_POST['user_email'])) {
                $u_username = htmlspecialchars($_POST['user_username']);
                $u_email = htmlspecialchars($_POST['user_email']);
                $u_password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
                
                $sql_user = "INSERT INTO users (username, email, password, role, related_id, is_active) VALUES (?, ?, ?, 'teacher', ?, 1)";
                $stmt_user = $pdo->prepare($sql_user);
                $stmt_user->execute([$u_username, $u_email, $u_password, $new_teacher_id]);
            }
        }
        
        $pdo->commit();
        header("Location: ../../dashboard/index.php?page=teachers&status=success");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Handle constraint violation (e.g. duplicate NIP)
        header("Location: ../../dashboard/index.php?page=teachers&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=teachers");
    exit();
}
?>
