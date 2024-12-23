<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'registrar') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $registrar_id = $_GET['id'];

    if (empty($registrar_id) || !is_numeric($registrar_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_registrar.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_admin` WHERE `id` = :registrar_id AND `role` = 'registrar'";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':registrar_id', $registrar_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_admin` WHERE `id` = :registrar_id AND `role` = 'registrar'";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':registrar_id', $registrar_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Deleted successfully!';
            header('Location: manage_registrar.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: manage_registrar.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: manage_registrar.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: manage_registrar.php');
    exit();
}
