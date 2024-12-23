<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'deans') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $issuance_id = $_GET['id'];

    if (empty($issuance_id) || !is_numeric($issuance_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: enrollment_schedules.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_deans_users_issuance` WHERE `id` = :issuance_id";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':issuance_id', $issuance_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_deans_users_issuance` WHERE `id` = :issuance_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':issuance_id', $issuance_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Schedule deleted successfully!';
            header('Location: enrollment_schedules.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: enrollment_schedules.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: enrollment_schedules.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: enrollment_schedules.php');
    exit();
}
