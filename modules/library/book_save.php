<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = htmlspecialchars($_POST['code']);
    $isbn = htmlspecialchars($_POST['isbn']);
    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $publisher = htmlspecialchars($_POST['publisher']);
    $stock = intval($_POST['stock']);
    $shelf_location = htmlspecialchars($_POST['shelf_location']);

    try {
        $sql = "INSERT INTO books (code, isbn, title, author, publisher, stock, shelf_location) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $isbn, $title, $author, $publisher, $stock, $shelf_location]);
        
        header("Location: ../../dashboard/index.php?page=library&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=library&action=add_book&error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
