<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize Inputs
    $full_name = htmlspecialchars($_POST['full_name']);
    $nisn = htmlspecialchars($_POST['nisn']);
    $gender = $_POST['gender'];
    $birth_place = htmlspecialchars($_POST['birth_place']);
    $birth_date = $_POST['birth_date'];
    $origin_school = htmlspecialchars($_POST['origin_school']);
    $address = htmlspecialchars($_POST['address']);
    $father_name = htmlspecialchars($_POST['father_name']);
    $mother_name = htmlspecialchars($_POST['mother_name']);
    $parent_phone = htmlspecialchars($_POST['parent_phone']);

    // 2. Handle File Uploads
    $upload_dir = '../../uploads/ppdb/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_kk_path = null;
    $file_akte_path = null;
    $file_ijazah_path = null;

    // Helper function for upload
    function upload_file($input_name, $dir) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $new_name = uniqid() . '_' . $input_name . '.' . $ext;
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $dir . $new_name)) {
                return 'uploads/ppdb/' . $new_name;
            }
        }
        return null;
    }

    $file_kk_path = upload_file('file_kk', $upload_dir);
    $file_akte_path = upload_file('file_akte', $upload_dir);
    $file_ijazah_path = upload_file('file_ijazah', $upload_dir);

    // 3. Insert Database
    try {
        $sql = "INSERT INTO ppdb_registrations 
                (full_name, nisn, gender, birth_place, birth_date, origin_school, address, father_name, mother_name, parent_phone, file_kk, file_akte, file_ijazah) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, $nisn, $gender, $birth_place, $birth_date, $origin_school, $address,
            $father_name, $mother_name, $parent_phone, $file_kk_path, $file_akte_path, $file_ijazah_path
        ]);

        // 4. Redirect with Success
        header("Location: ../../index.php?page=ppdb&status=success");
        exit();

    } catch (PDOException $e) {
        header("Location: ../../index.php?page=ppdb&error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../../index.php?page=ppdb");
    exit();
}
?>
