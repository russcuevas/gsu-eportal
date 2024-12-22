<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'clinic') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $clinic_id = $_GET['id'];

    if (empty($clinic_id) || !is_numeric($clinic_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_clinic.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_admin` WHERE `id` = :clinic_id AND `role` = 'clinic'";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':clinic_id', $clinic_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_admin` WHERE `id` = :clinic_id AND `role` = 'clinic'";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':clinic_id', $clinic_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Deleted successfully!';
            header('Location: manage_clinic.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: manage_clinic.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_clinic.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: manage_clinic.php');
    exit();
}
