<?php
session_start();
include '../database/connection.php';

// Ensure the user is authenticated and authorized
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id || $_SESSION['role'] !== 'clinic') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $appointed_at = $_POST['appointed_at'] ?? null;

    if ($request_id && $appointed_at) {
        try {
            $query = "
                UPDATE tbl_clinic_request 
                SET appointed_at = :appointed_at 
                WHERE id = :request_id
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':appointed_at', $appointed_at);
            $stmt->bindParam(':request_id', $request_id);

            //smtp

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
