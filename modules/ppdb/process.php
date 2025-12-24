<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    header("Location: ../../dashboard/index.php?page=ppdb");
    exit();
}

try {
    if ($action == 'approve' || $action == 'accept') {
        $pdo->beginTransaction();

        // 1. Get PPDB Data
        $stmt = $pdo->prepare("SELECT * FROM ppdb_registrations WHERE id = ?");
        $stmt->execute([$id]);
        $reg = $stmt->fetch();

        if (!$reg) {
            throw new Exception("Data not found");
        }

        if ($reg['status'] == 'Accepted') {
             throw new Exception("Already accepted.");
        }

        // 2. Insert into Students
        $sqlScore = "INSERT INTO students (full_name, nisn, gender, birth_place, birth_date, parent_phone, address) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($sqlScore);
        $stmtInsert->execute([
            $reg['full_name'], 
            $reg['nisn'], 
            $reg['gender'], 
            $reg['birth_place'], 
            $reg['birth_date'], 
            $reg['parent_phone'], 
            $reg['address']
        ]);
        $student_id = $pdo->lastInsertId();

        // 3. Create User Account (Default password: nisn)
        $username = strtolower(str_replace(' ', '.', $reg['full_name'])); // simple username gen
        $password = password_hash($reg['nisn'], PASSWORD_DEFAULT); // use NISN as def pass
        $email = $username . "@student.skulsis.com"; // dummy email for now

        // Handle duplicate username by checking? (Skipping for brevity, assuming low volume)
        
        $sqlUser = "INSERT INTO users (username, email, password, role, related_id, is_active) 
                    VALUES (?, ?, ?, 'student', ?, 1)";
        $stmtUser = $pdo->prepare($sqlUser);
        try {
            $stmtUser->execute([$username, $email, $password, $student_id]);
        } catch (Exception $e) {
            // If dup username, try append random
            $username .= rand(100,999);
            $email = $username . "@student.skulsis.com";
            $stmtUser->execute([$username, $email, $password, $student_id]);
        }

        // 4. Update PPDB Status
        $update = $pdo->prepare("UPDATE ppdb_registrations SET status = 'Accepted' WHERE id = ?");
        $update->execute([$id]);

        $pdo->commit();
        header("Location: ../../dashboard/index.php?page=ppdb&status=success&msg=Siswa diterima dan Akun dibuat.");
        exit();

    } elseif ($action == 'reject') {
        $stmt = $pdo->prepare("UPDATE ppdb_registrations SET status = 'Rejected' WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../../dashboard/index.php?page=ppdb&status=success&msg=Pendaftaran ditolak.");
        exit();
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: ../../dashboard/index.php?page=ppdb&error=" . urlencode($e->getMessage()));
    exit();
}
?>
