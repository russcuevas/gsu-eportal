<?php
include '../database/connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'osds') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_POST['id'])) {
    $requirements_id = $_POST['id'];

    if (empty($requirements_id) || !is_numeric($requirements_id)) {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: posted_requirements.php');
        exit();
    }

    $check_query = "SELECT * FROM `tbl_osds_post_requirements` WHERE `id` = :requirements_id";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bindParam(':requirements_id', $requirements_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $delete_query = "DELETE FROM `tbl_osds_post_requirements` WHERE `id` = :requirements_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':requirements_id', $requirements_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Requirements deleted successfully!';
            header('Location: posted_requirements.php');
            exit();
        } else {
            $_SESSION['error'] = 'There was an error during deletion!';
            header('Location: posted_requirements.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'There was an error during deletion!';
        header('Location: posted_requirements.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'There was an error during deletion!';
    header('Location: posted_requirements.php');
    exit();
}
