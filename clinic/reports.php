<?php
include '../database/connection.php';

// session
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
}

if ($_SESSION['role'] !== 'clinic') {
    header('location:../admin_login.php');
    exit();
}

$query = "
    SELECT 
        r.id AS request_id,
        r.request_number,
        r.laboratory_request,
        r.with_med_cert,
        r.med_cert_picture,
        r.status AS request_status,
        r.requested_at,
        r.appointed_at,
        u.id AS user_id,
        u.student_id,
        u.fullname,
        u.age,
        u.email,
        u.year,
        u.course,
        u.gender,
        u.status AS user_status
    FROM tbl_clinic_request r
    LEFT JOIN tbl_users u ON r.user_id = u.id
    WHERE r.status = 'Completed'
    ORDER BY r.requested_at DESC
";


$stmt = $conn->prepare($query);
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
                            <a href="manage_clinic.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Clinic
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="manage_request.php" class="nav-link">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    Request
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="accepted_request.php" class="nav-link">
                                <i class="nav-icon fas fa-check"></i>
                                <p>
                                    Accepted Appointment
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link active">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>
                                    Completed Reports
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
                                <li class="breadcrumb-item active">COMPLETED REPORTS</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">COMPLETED REPORTS</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>R. Number</th>
                                                <th>For Laboratory</th>
                                                <th>Fullname</th>
                                                <th>W/ Med-Cert</th>
                                                <th>Email</th>
                                                <th>Appointed At</th>
                                                <th>Status</th>
                                                <th>
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($requests as $request): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($request['request_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($request['laboratory_request']); ?></td>
                                                    <td><?php echo htmlspecialchars($request['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($request['with_med_cert']); ?></td>

                                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($request['appointed_at']); ?></td>
                                                    <td><?php echo htmlspecialchars($request['request_status']); ?></td>
                                                    <td>
                                                        <button class="btn btn-primary bg-blue" data-toggle="modal" data-target="#editAppointmentSchedule<?php echo $request['request_id']; ?>">View information</button>
                                                    </td>

                                                    <!-- update modal -->
                                                    <div class="modal fade" id="editAppointmentSchedule<?php echo $request['request_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $request['request_id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel<?php echo $request['request_id']; ?>"><?php echo $request['request_number']; ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <input type="hidden" id="request_id" name="request_id" value="<?php echo $request['request_id']; ?>">
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
                                                                            <strong>Status:</strong> <?php echo $request['request_status']; ?><br>
                                                                            <strong>Laboratory Test:</strong> <?php echo $request['laboratory_request']; ?><br>
                                                                            <strong>MEDICAL CERTIFICATE:</strong><br>
                                                                            <a class="" href="<?php echo "../assets/uploads/medical_certificate/" . htmlspecialchars($request['med_cert_picture']); ?>" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                                View Medical
                                                                            </a>
                                                                        <?php else: ?>
                                                                            <strong>Requested Date:</strong>
                                                                            <?php echo date('F d Y / g:ia', strtotime($request['requested_at']));
                                                                            ?><br>
                                                                            <strong>Appointed At:</strong> <?php echo date('F d Y / g:ia', strtotime($request['appointed_at'])); ?><br>
                                                                            <strong>Status:</strong> <?php echo $request['request_status']; ?><br>
                                                                            <strong>Laboratory Test:</strong> <?php echo $request['laboratory_request']; ?>
                                                                        <?php endif ?>

                                                                        <div class="d-flex justify-content-end" style="gap: 3px !important;">
                                                                            <a href="reports.php" class="btn btn-secondary">CLOSE</a>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /.modal -->
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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