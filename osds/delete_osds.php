<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'osds') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_POST['id'])) {
    $osds_id = $_POST['id'];

    if (empty($osds_id) || !is_numeric($osds_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_osds.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_admin` WHERE `id` = :osds_id AND `role` = 'osds'";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':osds_id', $osds_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_admin` WHERE `id` = :osds_id AND `role` = 'osds'";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':osds_id', $osds_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Deleted successfully!';
            header('Location: manage_osds.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: manage_osds.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_osds.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: manage_osds.php');
    exit();
}
