<?php
session_start();
include '../database/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
$query = "
    SELECT 
        r.id, 
        r.request_number, 
        r.laboratory_request, 
        r.with_med_cert,
        r.med_cert_picture, 
        r.status, 
        r.requested_at, 
        r.appointed_at,
        u.fullname, 
        u.student_id, 
        u.course, 
        u.year, 
        u.email
    FROM tbl_clinic_request r
    JOIN tbl_users u ON r.user_id = u.id
    WHERE r.user_id = :user_id
    ORDER BY r.requested_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <a href="../index.php" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>
                                    Homepage
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="my_request_documents.php" class="nav-link">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>
                                    My Document Request
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="my_request_medical.php" class="nav-link active">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    My Medical Request
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
                                <li class="breadcrumb-item active">MY MEDICAL REQUEST</li>
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
                            <div class="card">
                                <div style="background-color: #001968 !important; color: whitesmoke !important" class="card-header">
                                    <h3 class="card-title" style="font-size: 25px;">MY MEDICAL REQUEST</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <button onclick="window.location.href='../medical_request.php';" class="btn btn-primary mb-3">+ REQUEST MEDICAL</button>
                                    <div style="overflow:auto;">
                                        <table id="myTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Request Number</th>
                                                    <th>For Laboratory</th>
                                                    <th>With Med-Cert</th>
                                                    <th>Status</th>
                                                    <th>Requested At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($requests as $request): ?>
                                                    <tr>
                                                        <td><?php echo $request['request_number']; ?></td>
                                                        <td>
                                                            <?php echo ucfirst($request['laboratory_request']); ?>
                                                        </td>
                                                        <td><?php echo ucfirst($request['with_med_cert']); ?></td>

                                                        <td><?php echo ucfirst($request['status']); ?></td>
                                                        <td><?php echo ucfirst($request['requested_at']); ?></td>
                                                        <td>
                                                            <?php if ($request['status'] == 'pending'): ?>
                                                                <form action="cancel_medical_request.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                                    <input type="hidden" name="request_number" value="<?php echo $request['request_number']; ?>">
                                                                    <button type="submit" class="btn btn-danger">Cancel</button>
                                                                </form>
                                                            <?php elseif ($request['status'] === 'Accepted'): ?>
                                                                <button class="btn btn-primary bg-blue" data-toggle="modal" data-target="#viewAppointment<?php echo $request['id']; ?>">View appointment</button>
                                                                <div class="modal fade" id="viewAppointment<?php echo $request['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewAppointment<?php echo $request['id']; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="viewAppointment<?php echo $request['id']; ?>"><?php echo $request['request_number']; ?></h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="">
                                                                                    <input type="hidden" id="id" name="id" value="<?php echo $request['id']; ?>">
                                                                                    <input type="hidden" class="form-control" id="request_number" name="request_number" value="<?php echo $request['request_number']; ?>" readonly>
                                                                                    <strong>Fullname:</strong> <?php echo $request['fullname']; ?><br>
                                                                                    <strong>Laboratory:</strong> <?php echo $request['laboratory_request']; ?><br>
                                                                                    <strong>With Medical Certificate:</strong> <?php echo $request['with_med_cert']; ?><br>
                                                                                    <strong>Student ID:</strong> <?php echo $request['student_id']; ?><br>
                                                                                    <strong>Course:</strong> <?php echo $request['course']; ?><br>
                                                                                    <strong>Year:</strong> <?php echo $request['year']; ?><br>
                                                                                    <strong>Email:</strong> <?php echo $request['email']; ?>

                                                                                    <hr>

                                                                                    <strong>Requested Date:</strong>
                                                                                    <?php echo date('F d Y / g:ia', strtotime($request['requested_at']));
                                                                                    ?><br>
                                                                                    <strong>Appointed At:</strong>
                                                                                    <span style="color: red;"><?php echo date('F d Y / g:ia', strtotime($request['appointed_at']));
                                                                                                                ?></span><br>

                                                                                    <div class="d-flex justify-content-end" style="gap: 3px !important;">
                                                                                        <a href="my_request_medical.php" class="btn btn-secondary">CLOSE</a>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
                                                                <button class="btn btn-primary bg-blue" data-toggle="modal" data-target="#viewMyRequestMedical<?php echo $request['id']; ?>">View information</button>
                                                                <div class="modal fade" id="viewMyRequestMedical<?php echo $request['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewMyMedical<?php echo $request['id']; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="viewMyMedical<?php echo $request['id']; ?>"><?php echo $request['request_number']; ?></h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="" method="POST" enctype="multipart/form-data">
                                                                                    <input type="hidden" id="id" name="id" value="<?php echo $request['id']; ?>">
                                                                                    <input type="hidden" class="form-control" id="request_number" name="request_number" value="<?php echo $request['request_number']; ?>" readonly>
                                                                                    <strong>Fullname:</strong> <?php echo $request['fullname']; ?><br>
                                                                                    <strong>With Medical Certificate:</strong> <?php echo $request['with_med_cert']; ?><br>
                                                                                    <strong>Student ID:</strong> <?php echo $request['student_id']; ?><br>
                                                                                    <strong>Course:</strong> <?php echo $request['course']; ?><br>
                                                                                    <strong>Year:</strong> <?php echo $request['year']; ?><br>
                                                                                    <strong>Email:</strong> <?php echo $request['email']; ?><br>

                                                                                    <hr>

                                                                                    <?php if ($request['with_med_cert'] === 'Yes'): ?>
                                                                                        <strong>Requested Date:</strong>
                                                                                        <?php echo date('F d Y / g:ia', strtotime($request['requested_at']));
                                                                                        ?><br>
                                                                                        <strong>Appointed At:</strong> <?php echo date('F d Y / g:ia', strtotime($request['appointed_at'])); ?><br>
                                                                                        <strong>Status:</strong> <?php echo $request['status']; ?><br>
                                                                                        <strong>Laboratory Test:</strong> <?php echo $request['laboratory_request']; ?><br>
                                                                                        <strong>MEDICAL CERTIFICATE:</strong><br>
                                                                                        <?php if (isset($request['med_cert_picture']) && !empty($request['med_cert_picture'])): ?>
                                                                                            <a href="<?php echo "../assets/uploads/medical_certificate/" . htmlspecialchars($request['med_cert_picture']); ?>" target="_blank">
                                                                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> VIEW MY MEDICAL CERTIFICATE
                                                                                            </a>
                                                                                        <?php else: ?>
                                                                                            <span>Empty</span>
                                                                                        <?php endif; ?>
                                                                                    <?php else: ?>
                                                                                        <strong>Requested Date:</strong>
                                                                                        <?php echo date('F d Y / g:ia', strtotime($request['requested_at']));
                                                                                        ?><br>
                                                                                        <strong>Appointed At:</strong> <?php echo date('F d Y / g:ia', strtotime($request['appointed_at'])); ?><br>
                                                                                        <strong>Status:</strong> <?php echo $request['status']; ?><br>
                                                                                        <strong>Laboratory Test:</strong> <?php echo $request['laboratory_request']; ?>
                                                                                    <?php endif ?>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!-- /.row -->
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
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({

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



</body>

</html>