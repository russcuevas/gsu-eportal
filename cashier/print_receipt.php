<?php
include '../database/connection.php';

$request_number = $_GET['request_number'] ?? null;
if (!$request_number) {
    die('Request number is missing');
}

$query = "
    SELECT 
        dr.*, 
        u.fullname, 
        u.student_id, 
        u.year, 
        u.course, 
        u.email, 
        dr.payment_proof,  
        dr.gcash_reference_number
    FROM 
        tbl_document_request dr
    LEFT JOIN 
        tbl_users u ON dr.user_id = u.id 
    WHERE 
        dr.request_number = :request_number
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':request_number', $request_number);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch documents associated with this request_number
$query_documents = "
    SELECT 
        dr.documents_id, 
        dr.total_price, 
        dr.number_of_copies, 
        d.type_of_documents
    FROM tbl_document_request dr
    LEFT JOIN tbl_documents d ON dr.documents_id = d.id
    WHERE dr.request_number = :request_number
";
$stmt_documents = $conn->prepare($query_documents);
$stmt_documents->bindParam(':request_number', $request_number);
$stmt_documents->execute();
$documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

$total_price_sum = 0;
foreach ($documents as $document) {
    $total_price_sum += $document['total_price'];
}
?>

<!-- Receipt HTML to be injected into the modal -->
<div class="receipt">
    <div class="header">
        <div style="display: flex; align-items: center;">
            <img style="height: 50px; margin-right: 10px;" src="images/gsu-logo.jpg" alt="Guimaras State University Logo">
            <h4 style="margin: 0;">Guimaras State University</h4>
        </div>

        <div class="details">
            <strong>Request Number:</strong> <?php echo $request['request_number']; ?><br>
            <strong>Fullname:</strong> <?php echo $request['fullname']; ?><br>
            <strong>Student ID:</strong> <?php echo $request['student_id']; ?><br>
            <strong>Course:</strong> <?php echo $request['course']; ?><br>
            <strong>Year:</strong> <?php echo $request['year']; ?><br>
            <strong>Email:</strong> <?php echo $request['email']; ?><br>
            <strong>Status:</strong> <?php echo ucfirst($request['status']); ?><br>
            <strong>GCash REFERENCE NUMBER:</strong> <?php echo $request['gcash_reference_number']; ?><br>
        </div>
    </div>

    <div class="documents">
        <table class="table">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Copies</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?php echo $document['type_of_documents']; ?></td>
                        <td><?php echo $document['number_of_copies']; ?></td>
                        <td>₱<?php echo number_format($document['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="total">
        <p>Total: ₱<?php echo number_format($total_price_sum, 2); ?></p>
    </div>
</div>