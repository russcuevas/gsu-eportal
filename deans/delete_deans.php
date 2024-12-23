<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'deans') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $dean_id = $_GET['id'];

    if (empty($dean_id) || !is_numeric($dean_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_deans.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_admin` WHERE `id` = :dean_id AND `role` = 'deans'";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':dean_id', $dean_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_admin` WHERE `id` = :dean_id AND `role` = 'deans'";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':dean_id', $dean_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Deleted successfully!';
            header('Location: manage_deans.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: manage_deans.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_deans.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: manage_deans.php');
    exit();
}
