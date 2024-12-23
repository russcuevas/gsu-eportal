<?php
include '../database/connection.php';
session_start();

// Check if the admin is logged in
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'cashier') {
    header('location:../admin_login.php');
    exit();
}

$request_number = $_GET['request_number'] ?? null;
if ($request_number === null) {
    $_SESSION['error'] = 'Request number is missing.';
    header('location:requests.php');
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
    header('location:requests.php');
    exit();
}

if (isset($_POST['approve'])) {
    //smtp for update only
    $update_query = "
        UPDATE tbl_document_request
        SET status = 'paid'
        WHERE request_number = :request_number
    ";

    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bindParam(':request_number', $request_number);

    if ($stmt_update->execute()) {
        $query = "
            SELECT 
                dr.*, 
                u.year, 
                u.course, 
                u.email,
                u.gender,  -- Fetch gender from tbl_users
                dr.status  -- Fetch status from tbl_document_request
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
                d.price  -- Fetch price from tbl_documents
            FROM tbl_document_request dr
            LEFT JOIN tbl_documents d ON dr.documents_id = d.id
            WHERE dr.request_number = :request_number
        ";

        $stmt_documents = $conn->prepare($query_documents);
        $stmt_documents->bindParam(':request_number', $request_number);
        $stmt_documents->execute();
        $documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documents as $document) {
            $insert_query = "
                INSERT INTO tbl_document_reports (
                    student_id, fullname, email, year, course, gender, 
                    request_number, type_of_documents, price, 
                    number_of_copies, total_price, payment_method, 
                    payment_proof, gcash_reference_number, status, created_at
                ) 
                VALUES (
                    :student_id, :fullname, :email, :year, :course, :gender, 
                    :request_number, :type_of_documents, :price, 
                    :number_of_copies, :total_price, :payment_method, 
                    :payment_proof, :gcash_reference_number, :status, :created_at
                )
            ";

            $stmt_insert = $conn->prepare($insert_query);
            $stmt_insert->bindParam(':student_id', $request['student_id']);
            $stmt_insert->bindParam(':fullname', $request['fullname']);
            $stmt_insert->bindParam(':email', $request['email']);
            $stmt_insert->bindParam(':year', $request['year']);
            $stmt_insert->bindParam(':course', $request['course']);
            $stmt_insert->bindParam(':gender', $request['gender']);
            $stmt_insert->bindParam(':request_number', $request['request_number']);
            $stmt_insert->bindParam(':type_of_documents', $document['type_of_documents']);
            $stmt_insert->bindParam(':price', $document['price']);
            $stmt_insert->bindParam(':number_of_copies', $document['number_of_copies']);
            $stmt_insert->bindParam(':total_price', $document['total_price']);
            $stmt_insert->bindParam(':payment_method', $request['payment_method']);
            $stmt_insert->bindParam(':payment_proof', $request['payment_proof']);
            $stmt_insert->bindParam(':gcash_reference_number', $request['gcash_reference_number']);
            $stmt_insert->bindParam(':status', $request['status']);
            $stmt_insert->bindParam(':created_at', $request['created_at']);

            if (!$stmt_insert->execute()) {
                $_SESSION['error'] = 'An error occurred while inserting document into the report.';
                break;
            }
        }

        $_SESSION['success'] = 'Request has been approved and marked as paid.';
    } else {
        $_SESSION['error'] = 'An error occurred while approving the request.';
    }

    header("Location: manage_request.php");
    exit();
}


if (isset($_POST['disapprove'])) {
    $delete_query = "
        DELETE FROM tbl_document_request 
        WHERE request_number = :request_number
    ";

    //smtp

    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bindParam(':request_number', $request_number);

    if ($stmt_delete->execute()) {
        if ($request['payment_proof'] && file_exists('../assets/uploads/gcash_proofs/' . $request['payment_proof'])) {
            unlink('../assets/uploads/gcash_proofs/' . $request['payment_proof']);
        }

        $_SESSION['success'] = 'Request has been disapproved and deleted successfully.';
    } else {
        $_SESSION['error'] = 'An error occurred while disapproving the request.';
    }
    header("Location: manage_request.php");
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
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
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
                            <a href="manage_cashier.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Cashier
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="manage_documents.php" class="nav-link">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>
                                    Manage Documents
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="manage_request.php" class="nav-link active">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    Manage Request
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="reports.php" class="nav-link">
                                <i class="nav-icon fas fa-check"></i>
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
                                <li class="breadcrumb-item"><a href="manage_request.php">MANAGE REQUEST</a></li>
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
                                            <?php if ($request['status'] === 'pending'): ?>
                                                <form action="" method="POST">
                                                    <button type="submit" name="approve" class="btn btn-primary"></i> Approved <i class="fa fa-check" aria-hidden="true"></i></button>
                                                    <button type="submit" name="disapprove" class="btn btn-danger" style="margin-right: 10px;">Disapproved <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#receiptModal" data-request-number="<?php echo $request['request_number']; ?>">
                                                    Print Receipt <i class="fa fa-print" aria-hidden="true"></i>
                                                </a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="receiptModalLabel"><?php echo $request['request_number']; ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" id="receiptContent">

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="printButton">Print Receipt</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <!-- ChartJS -->
    <script src=" plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });
        });
    </script>

    <script>
        function setDocumentsId(DocumentId) {
            document.getElementById('document_id_delete').value = DocumentId;
        }
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

    <!-- ajax printing -->
    <script>
        $('#receiptModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var requestNumber = button.data('request-number');
            $.ajax({
                url: 'print_receipt.php',
                method: 'GET',
                data: {
                    request_number: requestNumber
                },
                success: function(response) {
                    $('#receiptContent').html(response);
                }
            });
        });

        $('#printButton').on('click', function() {
            var printContent = document.getElementById('receiptContent').innerHTML;
            var newWindow = window.open('', '', 'height=400,width=600');
            newWindow.document.write('<html><head><title>Print Receipt</title></head><body>');
            newWindow.document.write(printContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        });
    </script>

</body>

</html>