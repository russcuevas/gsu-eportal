<?php
session_start();
require 'database/connection.php';


// READ DOCUMENTS
$get_documents = "SELECT * FROM `tbl_documents`";
$stmt_get_documents = $conn->query($get_documents);
$documents = $stmt_get_documents->fetchAll(PDO::FETCH_ASSOC);
// END READ DOCUMENTS


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $document_ids = $_POST['document_ids'] ?? [];
        $number_of_copies = $_POST['number_of_copies'] ?? [];
        $gcash_reference_number = $_POST['gcash_reference_number'] ?? '';

        if (empty($document_ids)) {
            $_SESSION['warning'] = "Please select at least one document";
            header("Location: document_request.php");
            exit();
        }

        $payment_proof = '';
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
            $payment_proof_file = $_FILES['payment_proof'];
            $original_name = basename($payment_proof_file['name']);
            $target_dir = "assets/uploads/gcash_proofs/";

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $original_name;
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);

            while (file_exists($target_file)) {
                $original_name = pathinfo($original_name, PATHINFO_FILENAME) . '_' . time() . '.' . $file_extension;
                $target_file = $target_dir . $original_name;
            }

            if (move_uploaded_file($payment_proof_file['tmp_name'], $target_file)) {
                $payment_proof = $original_name;
            } else {
                $_SESSION['error'] = 'Failed to upload the image.';
                header('Location: document_request.php');
                exit();
            }
        }

        foreach ($document_ids as $document_id) {
            $stmt = $conn->prepare("SELECT * FROM tbl_documents WHERE id = ?");
            $stmt->execute([$document_id]);
            $document = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($document) {
                $copies = isset($number_of_copies[$document_id]) ? $number_of_copies[$document_id] : 1;
                $total_price = $document['price'] * $copies;
                $request_number = 'REQ' . time();

                $stmt_insert = $conn->prepare("INSERT INTO tbl_document_request (user_id, documents_id, request_number, fullname, student_id, number_of_copies, total_price, status, payment_method, payment_proof, gcash_reference_number, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

                $fullname = $_SESSION['fullname'];
                $student_id = $_SESSION['student_id'];
                $status = 'pending';
                $payment_method = 'GCash';

                $stmt_insert->execute([
                    $user_id,
                    $document_id,
                    $request_number,
                    $fullname,
                    $student_id,
                    $copies,
                    $total_price,
                    $status,
                    $payment_method,
                    $payment_proof,
                    $gcash_reference_number
                ]);
            }
        }

        //smtp


        $_SESSION['success'] = "Your document request has been submitted successfully.";
        header("Location: document_request.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to submit. Please log in first.";
        header("Location: document_request.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="Images/logo.png">
    <title>GSU | e-Request</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/6934bb79c3.js"></script>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item {
            margin: 0 5px;
        }

        .page-link {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            background-color: #00529B;
            border: 1px solid #004080;
            border-radius: 4px;
        }

        .page-item.active .page-link {
            background-color: #003366;
            color: white;
        }

        .page-link:hover {
            background-color: #004080;
        }

        .dropdown-button.active {
            background-color: #fff;
            color: #004080 !important;
        }

        .dropdown-link.active {
            background-color: #fff;
            color: #004080 !important;
            font-weight: 900 !important;
        }
    </style>
</head>

<body>
    <div id="myTopnav">
        <div class="logo">
            <a id="home" href="index.php" class="logo-link"><img src="assets/images/gsu-logo.jpg" alt="Logo" style="width:50px; border-radius: 50px;"> &nbsp; <span style="color: white; font-weight: 900;">GSU | e-Request</span></a>
        </div>
        <a href="javascript:void(0);" class="show-nav" onclick="sideNav()"><i class="fa fa-bars"></i></a>
        <ul id="navbar" class="navbar-menu">
            <li class="navbar-item hide-nav" onclick="sideNav()">
                <a class="item-link" href="javascript:void(0);"><i class="fa fa-times"></i></a>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="index.php">HOME</a>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="requirements.php">REQUIREMENTS</a>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="#">SCHEDULES</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="class_schedules.php" id="">Class Schedules</a>
                        <a class="dropdown-link" href="enrollment_schedules.php" id="">Enrollment Schedules</a>
                    </div>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button active" href="#">e-Request</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link active" href="document_request.php" id="">Document Request</a>
                        <a class="dropdown-link" href="medical_request.php" id="">Medical Request</a>
                    </div>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="item-link" href="#">e-Portal</a>
                    <div class="dropdown-content" style="width:250px">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="dropdown-link" href="student_portal/dashboard.php" id="">Student Portal</a>
                            <a class="dropdown-link" href="logout.php" id="">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-link" href="login.php" id="">Student Portal</a>
                            <a class="dropdown-link" href="admin_login.php" id="">Employee Portal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <!-- Page Content -->
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <form method="post" class="col-12 col-md-8 p-4 bg-light rounded" enctype="multipart/form-data">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="student_id" value="<?php echo $_SESSION['student_id']; ?>">
                    <input type="hidden" name="fullname" value="<?php echo $_SESSION['fullname']; ?>">
                <?php else: ?>
                <?php endif; ?>
                <figure>
                    <blockquote class="blockquote">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <p>Please check the information of your account if it is correct.</p>
                        <?php else: ?>
                        <?php endif; ?>
                    </blockquote>

                </figure>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <div class="mb-4">
                        <h5>Your Information:</h5>
                        <div class="form-group">
                            <label for="">
                                <span>Student ID: <?php echo $_SESSION['student_id']; ?></span><br>
                                <span>Email: <?php echo $_SESSION['email']; ?></span><br>
                                <span>Fullname: <?php echo $_SESSION['fullname']; ?></span><br>
                                <span>Age: <?php echo $_SESSION['age']; ?></span><br>
                                <span>Year & Course: <?php echo $_SESSION['year']; ?> - <?php echo $_SESSION['course'] ?></span><br>
                                <span style="text-transform: capitalize;">Gender: <?php echo $_SESSION['gender']; ?></span><br>
                                <span style="text-transform: capitalize;">Status: <?php echo $_SESSION['status']; ?></span><br>
                            </label>
                            <span class="text-danger field-validation-valid" data-valmsg-for="" data-valmsg-replace="true"></span>
                        </div>
                    </div>
                <?php else: ?>
                <?php endif; ?>

                <?php if (!empty($documents)): ?>

                    <section>
                        <h5>AVAILABLE DOCUMENT:</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Document Type</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $document): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <?php if (
                                                ($_SESSION['status'] === 'graduate' && $document['type_of_documents'] === 'CAV-UG') ||
                                                ($_SESSION['status'] === 'old' && $document['type_of_documents'] === 'CAV-G')
                                            ): ?>
                                                <?php continue; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="document_ids[]" value="<?php echo $document['id']; ?>" class="document-selection" />
                                            </td>
                                            <td><?php echo $document['type_of_documents']; ?></td>
                                            <td>₱<?php echo $document['price']; ?></td>
                                            <td>
                                                <input type="number" disabled name="number_of_copies[<?php echo $document['id']; ?>]" value="1" class="form-control quantity-input" data-price="<?php echo $document['price']; ?>" min="1" />
                                            </td>
                                            <td class="document-total">₱0.00</td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                        <div class="text-end float-right">
                            <strong>Prepare a Total Amount of:</strong>
                            <span id="totalAmount" style="color: red; font-weight: 900;">₱0.00</span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="form-group mt-3">
                                    <label for="payment_proof">GCash Payment Proof (Image)</label>
                                    <input type="file" name="payment_proof" class="form-control" accept="image/*" id="payment_proof" required />
                                </div>

                                <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                                    <h6>Selected Image Preview:</h6>
                                    <a href="#" id="imagePreviewLink" target="_blank">
                                        <img id="imagePreview" src="" alt="Payment Proof Preview" class="img-fluid" style="max-width: 300px; height: 300px; cursor: pointer;" />
                                    </a>
                                </div>

                                <div id="imageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="imageModalLabel">Payment Proof</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body d-flex justify-content-center align-items-center">
                                                <img id="modalImage" src="" alt="Full-size Payment Proof" class="img-fluid" style="max-height: 500px; max-width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="gcash_reference_number">GCash Reference Number</label>
                                    <input type="text" name="gcash_reference_number" class="form-control" id="gcash_reference_number" required />
                                </div>

                                <button type="submit" class="btn btn-primary bg-blue mt-3">
                                    <i class="fas fa-paper-plane"></i> SUBMIT
                                </button>
                            <?php else: ?>
                                <br>
                                <a href="login.php" class="btn btn-danger">LOGIN FIRST TO REQUEST</a>
                            <?php endif; ?>
                        </div>

                        <div class="mt-5">
                            <p class="text-muted">
                                <strong style="color: red">Instructions:</strong><br>
                                <strong>Your request will be submitted for validation.</strong><br>
                                <strong>The cashier will check if you are paid in your request based on your uploaded payment proof</strong><br>
                                <strong>Once your payment proof is approved by the cashier, the system will give you a receipt and you can get your requested document in the registrar by showing the receipt.</strong>
                                <br><strong>Check your status here <a href="student_portal/my_request_documents.php">CLICK HERE</a></strong>
                            </p>
                        </div>
                    </section>
                <?php else: ?>
                    <div style="background-color: #001968; color:whitesmoke; padding: 50px;">
                        <h4 style="text-align: center;">No available documents at the moment.</h4>
                    </div>
                <?php endif; ?>

            </form>
        </div>
    </div>
    <!-- Reuse Footer -->
    <div id="footer" class="footer pt-3 pb-2" style="background-color: #001968">
        <div class="container">
            <div class="text-white">
                <div class="row">
                    <div class="col-md-8">
                        <p style="font-family: CENTURY GOTHIC; font-size: 10px;">
                        <table style="width: 100%; font-family: CENTURY GOTHIC; font-size: 10px; ">
                            <tr valign="top">
                                <td style="text-align: center;">
                                    <i class="fas fa-map-marker-alt" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td>
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        Mclain,<br style="margin-top: 0px; margin-bottom: 0px">
                                        Buenavista,<br style="margin-top: 0px; margin-bottom: 0px">
                                        Guimaras<br style="margin-top: 0px; margin-bottom: 0px">
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="text-align: center;">
                                    <i class="fa fa-phone" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td>
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        (033) 580 8244<br style="margin-top: 0px; margin-bottom: 0px">
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-top: 10px; text-align: center;">
                                    <i class="fab fa-google" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td style="padding-top: 8px;">
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        university.president@gsu.edu.ph
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-top: 10px; text-align: center;">
                                    <i class='fab fa-facebook-f' style='font-size:16px; color: #fff'></i>
                                </td>
                                <td style="padding-top: 8px; ">
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        <a href="https://www.facebook.com/GuimarasStateUniversity" style="text-decoration: none; color: white">facebook.com/GuimarasStateUniversity</a>
                                        <!--Messenger Link-->
                                        <!--m.me/WeFormHearts-->
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-right: 20px; text-align: center;">

                                </td>
                                <td style="padding-top: 40px">
                                    <a class="btn btn-link text-white" href="" id="loginpage" style="font-size: 10px">© Copyright 2024. All Rights Reserved.</a>
                                </td>
                            </tr>
                        </table>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '" . $_SESSION['error'] . "',
        });
    </script>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['warning'])) {
        echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: '" . $_SESSION['warning'] . "',
        });
    </script>";
        unset($_SESSION['warning']);
    }

    if (isset($_SESSION['success'])) {
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '" . $_SESSION['success'] . "',
        });
    </script>";
        unset($_SESSION['success']);
    }
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select the file input and the image preview container
            const paymentProofInput = document.getElementById('payment_proof');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewLink = document.getElementById('imagePreviewLink');
            const modalImage = document.getElementById('modalImage');

            // Ensure the elements are present before attaching event listeners
            if (!paymentProofInput || !imagePreviewContainer || !imagePreview || !imagePreviewLink || !modalImage) {
                console.error("Missing necessary HTML elements.");
                return;
            }

            // Listen for changes on the file input
            paymentProofInput.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    // Create a URL for the selected file and show the preview
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result; // Set the src of the image preview
                        imagePreviewLink.href = e.target.result; // Set the link to the full-size image
                        imagePreviewContainer.style.display = 'block'; // Show the preview container
                    };

                    reader.readAsDataURL(file); // Read the file as a Data URL
                } else {
                    // Hide the image preview container if no file is selected
                    imagePreviewContainer.style.display = 'none';
                }
            });

            // Open the full-size image in a modal when clicked
            imagePreviewLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default link behavior (opening in new tab)
                modalImage.src = imagePreview.src; // Set the modal image source to the preview image
                $('#imageModal').modal('show'); // Show the modal (Bootstrap 4)
            });
        });
    </script>

    <script>
        function updateTotals() {
            let totalAmount = 0;
            let selectedDocuments = document.querySelectorAll('.document-selection:checked');
            selectedDocuments.forEach(function(checkbox) {
                let row = checkbox.closest('tr');
                let quantityInput = row.querySelector('.quantity-input');
                let documentTotalCell = row.querySelector('.document-total');

                let price = parseFloat(quantityInput.getAttribute('data-price'));
                let quantity = parseInt(quantityInput.value);
                let documentTotal = price * quantity;
                documentTotalCell.textContent = '₱' + documentTotal.toFixed(2);
                totalAmount += documentTotal;
            });
            document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);
        }

        function toggleQuantityInput() {
            let checkbox = this;
            let row = checkbox.closest('tr');
            let quantityInput = row.querySelector('.quantity-input');

            if (checkbox.checked) {
                quantityInput.disabled = false;
            } else {
                quantityInput.disabled = true;
                quantityInput.value = 1;
                row.querySelector('.document-total').textContent = '₱0.00';
            }
            updateTotals();
        }
        document.querySelectorAll('.document-selection').forEach(function(checkbox) {
            checkbox.addEventListener('change', toggleQuantityInput);
        });

        document.querySelectorAll('.quantity-input').forEach(function(input) {
            input.addEventListener('input', updateTotals);
        });

        document.addEventListener('DOMContentLoaded', updateTotals);
    </script>
</body>

</html>