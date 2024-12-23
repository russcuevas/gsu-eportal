<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'cashier') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id']) && $_GET['id'] !== "") {
    $id = $_GET['id'];

    $query = "DELETE FROM tbl_documents WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $execute_result = $stmt->execute();

    if ($execute_result) {
        $_SESSION['success'] = 'Document deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting document.';
    }
    header('Location: manage_documents.php');
    exit();
} else {
    $_SESSION['error'] = 'Invalid document ID.';
    header('Location: manage_documents.php');
    exit();
}
