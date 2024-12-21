<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in first.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$request_number = $_POST['request_number'] ?? null;

if (!$request_number) {
    $_SESSION['error'] = "Request number is missing.";
    header("Location: my_request_medical.php");
    exit();
}

$query = "
    SELECT * 
    FROM tbl_clinic_request 
    WHERE request_number = :request_number AND user_id = :user_id
    LIMIT 1
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':request_number', $request_number);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    $_SESSION['error'] = "Request not found or you do not have permission to delete this request.";
    header("Location: my_request_medical.php");
    exit();
}

$delete_query = "
    DELETE FROM tbl_clinic_request
    WHERE request_number = :request_number AND user_id = :user_id
";
$stmt_delete = $conn->prepare($delete_query);
$stmt_delete->bindParam(':request_number', $request_number);
$stmt_delete->bindParam(':user_id', $user_id);

if ($stmt_delete->execute()) {
    $_SESSION['success'] = "Your medical request has been cancelled successfully.";
} else {
    $_SESSION['error'] = "Failed to delete the request. Please try again.";
}

header("Location: my_request_medical.php");
exit();
