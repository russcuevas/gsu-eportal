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
    $class_sched_id = $_GET['id'];

    if (empty($class_sched_id) || !is_numeric($class_sched_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: class_schedules.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_deans_post_class_schedules` WHERE `id` = :class_sched_id";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':class_sched_id', $class_sched_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_deans_post_class_schedules` WHERE `id` = :class_sched_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':class_sched_id', $class_sched_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Class schedule deleted successfully!';
            header('Location: class_schedules.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: class_schedules.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: class_schedules.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: class_schedules.php');
    exit();
}
