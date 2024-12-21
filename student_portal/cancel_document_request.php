<?php
include '../database/connection.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:../login.php');
    exit();
}

$request_number = $_POST['request_number'] ?? null;
if (!$request_number) {
    $_SESSION['error'] = 'Request number is missing.';
    header('location:my_request_documents.php');
    exit();
}

$query = "
    SELECT * 
    FROM tbl_document_request 
    WHERE request_number = :request_number 
    AND user_id = :user_id
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':request_number', $request_number);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    $_SESSION['error'] = 'Request not found or you do not have permission to cancel this request.';
    header('location:my_request_documents.php');
    exit();
}

$delete_documents_query = "
    DELETE FROM tbl_document_request 
    WHERE request_number = :request_number
";
$stmt_delete_documents = $conn->prepare($delete_documents_query);
$stmt_delete_documents->bindParam(':request_number', $request_number);

if ($stmt_delete_documents->execute()) {
    if ($request['payment_proof'] && file_exists('../assets/uploads/gcash_proofs/' . $request['payment_proof'])) {
        unlink('../assets/uploads/gcash_proofs/' . $request['payment_proof']);
    }

    $_SESSION['success'] = 'Request cancelled successfully.';
} else {
    $_SESSION['error'] = 'An error occurred while cancelling the request.';
}

header('location:my_request_documents.php');
exit();
