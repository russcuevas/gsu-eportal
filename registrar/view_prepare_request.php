<?php
include '../database/connection.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Check if the admin is logged in
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'registrar') {
    header('location:../admin_login.php');
    exit();
}

$request_number = $_GET['request_number'] ?? null;
if ($request_number === null) {
    $_SESSION['error'] = 'Request number is missing.';
    header('location:to_prepare_request.php');
    exit();
}

$query = "
    SELECT 
        dr.*, 
        u.year, 
        u.course, 
        u.email 
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

if (!$request) {
    $_SESSION['error'] = 'Request not found.';
    header('location:to_prepare_request.php');
    exit();
}

if (isset($_POST['claimable'])) {
    $update_query = "
        UPDATE tbl_document_request
        SET status = 'claimable'
        WHERE request_number = :request_number
    ";

    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bindParam(':request_number', $request_number);

    if ($stmt_update->execute()) {
        $update_reports_query = "
            UPDATE tbl_document_reports
            SET status = 'claimable'
            WHERE request_number = :request_number
        ";

        $stmt_reports_update = $conn->prepare($update_reports_query);
        $stmt_reports_update->bindParam(':request_number', $request_number);

        if (!$stmt_reports_update->execute()) {
            $_SESSION['error'] = 'Failed to update tbl_document_reports status.';
            header('Location: to_prepare_request.php');
            exit();
        }

        $query = "
            SELECT 
                dr.*, 
                u.year, 
                u.course, 
                u.email,
                u.gender,
                dr.status
            FROM 
                tbl_document_request dr
            LEFT JOIN 
                tbl_users u ON dr.user_id = u.id 
            WHERE 
                dr.request_number = :request_number
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':request_number', $request_number);
        $stmt->execute();
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        $query_documents = "
            SELECT 
                dr.documents_id, 
                dr.number_of_copies, 
                dr.total_price, 
                d.type_of_documents, 
                d.price
            FROM tbl_document_request dr
            LEFT JOIN tbl_documents d ON dr.documents_id = d.id
            WHERE dr.request_number = :request_number
        ";

        $stmt_documents = $conn->prepare($query_documents);
        $stmt_documents->bindParam(':request_number', $request_number);
        $stmt_documents->execute();
        $documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'guimarasrequestingsystem@gmail.com';
            $mail->Password = 'idyztzjuzwcrupwp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('gsu-erequest@gmail.com', 'Guimaras State University Registrar');
            $mail->addAddress($request['email'], $request['fullname']);

            $mail->isHTML(true);
            $mail->Subject = 'Your Document Request is Now Claimable';

            $mail_body = "<p>Dear {$request['fullname']},</p>
                          <p>We are pleased to inform you that your document request has been marked as claimable.</p>
                          <p>Office hours 8:00am - 5:00pm</p>
                          <p>Here is a summary of your requested documents:</p>
                          <table border='1' cellpadding='10' cellspacing='0'>
                              <tr>
                                  <th>Document Type</th>
                                  <th>Price</th>
                                  <th>Number of Copies</th>
                                  <th>Total Price</th>
                              </tr>";

            $total_price = 0;

            foreach ($documents as $document) {
                $document_type = $document['type_of_documents'];
                $price = $document['price'];
                $number_of_copies = $document['number_of_copies'];
                $document_total_price = $document['total_price'];

                $mail_body .= "<tr>
                                  <td>{$document_type}</td>
                                  <td>{$price}</td>
                                  <td>{$number_of_copies}</td>
                                  <td>{$document_total_price}</td>
                               </tr>";

                $total_price += $document_total_price;
            }

            $mail_body .= "</table>
                           <p><strong>Total Price: {$total_price}</strong></p>
                           <p>You may now proceed to claim your requested document.</p>
                           <p>If you have any questions, feel free to contact us.</p>
                           <p>Best regards,<br>Registrar Staff</p>";

            $mail->Body = $mail_body;
            $mail->send();

            $_SESSION['success'] = 'Request has been marked as claimable and email has been sent.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = 'An error occurred while updating the request status.';
    }

    header("Location: to_prepare_request.php");
    exit();
}

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
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GSU | e-Request</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../public/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="../public/plugins/toastr/toastr.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="../public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../public/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="../public/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../public/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="../public/plugins/summernote/summernote-bs4.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- hold -->
    <link rel="stylesheet" href="../public/dist/css/hold.css">
    <style>
        .nav-link.active {
            background-color: #FCC737 !important;
        }

        .btn.btn-primary {
            background-color: #001968 !important;
            border: #001968;
        }

        .btn.btn-primary:hover {
            background-color: black !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav style="background-color: #001968 !important; border-bottom: 3px solid #FCC737;" class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" style="color: whitesmoke !important;" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a style="background-color: #001968 !important; border-right: 1px solid #FCC737; border-bottom: 1px solid #FCC737;" href="index3.html" class="brand-link">
                <img src="images/gsu-logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light" style="color: whitesmoke !important;">GSU | e-Request</span>
            </a>

            <!-- Sidebar -->
            <div style="background-color: #001968 !important; border-right: 1px solid #FCC737;" class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_registrar.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Registrar
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="to_prepare_request.php" class="nav-link active">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    To Prepare Request
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="to_claim_request.php" class="nav-link">
                                <i class="nav-icon fas fa-check"></i>
                                <p>
                                    To Claim Request
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="reports.php" class="nav-link">
                                <i class="nav-icon fas fa-flag"></i>
                                <p>
                                    Reports
                                </p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">

                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">DASHBOARD</a></li>
                                <li class="breadcrumb-item"><a href="manage_request.php">PREPARE REQUEST</a></li>
                                <li class="breadcrumb-item active">VIEW REQUEST</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Main content -->
                            <div class="invoice p-3 mb-3">
                                <!-- title row -->
                                <div class="row">
                                    <div class="col-12">
                                        <h4>
                                            <img style="height: 50px;" src="images/gsu-logo.jpg" alt=""> Guimaras State University
                                            <small class="float-right"><?php echo $request['created_at']; ?></small>
                                        </h4>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        <address>
                                            <strong></strong><br>
                                            <?php echo $request['fullname']; ?><br>
                                            <?php echo $request['student_id']; ?><br>
                                            <span style="text-transform: capitalize;"><?php echo $request['year']; ?><br>
                                                <?php echo $request['course']; ?></span><br>
                                            <?php echo $request['email']; ?>
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">

                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        <span style="font-weight: 900;"><?php echo $request['request_number']; ?></span><br>
                                        <span style="text-transform: capitalize; font-weight: 900;"><?php echo $request['status']; ?></span><br>
                                        <?php if ($request['status'] === 'paid'): ?>
                                            <span style="color: green; font-weight: 900;">Approved by the cashier</span><br>
                                        <?php else: ?>
                                            <span style="color: green; font-weight: 900;">Already claimed</span><br>
                                        <?php endif; ?>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>DOCUMENT REQUEST</th>
                                                    <th>NUMBER OF COPIES</th>
                                                    <th>PRICE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($documents as $document):
                                                    $total_price_sum += $document['total_price'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $document['type_of_documents']; ?></td>
                                                        <td><?php echo $document['number_of_copies']; ?></td>
                                                        <td>₱<?php echo number_format($document['total_price'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">
                                    <!-- accepted payments column -->
                                    <div class="col-6">
                                        <p class="lead" style="color: black !important; font-weight: 900">GCASH PAYMENT PROOF:</p>
                                        <img style="height: 300px; max-width: 300px;" src="../assets/uploads/gcash_proofs/<?php echo $request['payment_proof']; ?>" alt="Visa"><br>
                                        <a href="../assets/uploads/gcash_proofs/<?php echo $request['payment_proof']; ?>" target="_blank">VIEW PAYMENT PROOF</a>

                                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px; color: black !important; font-weight: 900">
                                            GCASH REFERENCE NUMBER: <?php echo $request['gcash_reference_number']; ?>
                                        </p>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-6">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th>TOTAL:</th>
                                                    <td style="color: red; font-weight: 900;">₱<?php echo number_format($total_price_sum, 2); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div class="col-12">
                                        <div style="gap: 3px !important; display: flex; justify-content: flex-end;">
                                            <form action="" method="POST" id="claimable_form">
                                                <button type="submit" name="claimable" class="btn btn-primary">
                                                    Claimable <i class="fa fa-check" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.invoice -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../public/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="../public/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="../public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../public/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="../public/plugins/toastr/toastr.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <!-- ChartJS -->
    <script src=" ../public/plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="../public/plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="../public/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="../public/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="../public/plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="../public/plugins/moment/moment.min.js"></script>
    <script src="../public/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="../public/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/dist/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="../public/dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../public/dist/js/demo.js"></script>
    <!-- hold -->
    <script src="../public/dist/js/hold.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });
        });
    </script>


    <!-- success and error message alert -->
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['success'])): ?>
                toastr.success('<?php echo $_SESSION['success']; ?>');
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                toastr.error('<?php echo $_SESSION['error']; ?>');
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const claimableForm = document.getElementById('claimable_form');
            claimableForm.addEventListener('submit', function(event) {

                HoldOn.open({
                    theme: "sk-bounce",
                    message: "Processing your claimable request...",
                    backgroundColor: "rgba(0, 0, 0, 0.7)",
                    textColor: "white",
                    spinnerColor: "#fff"
                });

            });
        });
    </script>

</body>

</html>