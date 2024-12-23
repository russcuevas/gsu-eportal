<?php
include '../database/connection.php';

//session
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
}

// if not cashier role it will redirect to login
if ($_SESSION['role'] !== 'cashier') {
    header('location:../admin_login.php');
    exit();
}

// GET THE TOTAL CASHIER
$get_total_cashier = "SELECT COUNT(*) AS total_cashier FROM `tbl_admin` WHERE role = 'cashier'";
$stmt_total_cashier = $conn->prepare($get_total_cashier);
$stmt_total_cashier->execute();
$results_total_cashier = $stmt_total_cashier->fetch(PDO::FETCH_ASSOC);
$total_cashier = $results_total_cashier['total_cashier'];
// END GET TOTAL CASHIER

// GET THE TOTAL DOCUMENTS
$get_total_documents = "SELECT COUNT(*) AS total_documents FROM `tbl_documents`";
$stmt_total_documents = $conn->prepare($get_total_documents);
$stmt_total_documents->execute();
$results_total_documents = $stmt_total_documents->fetch(PDO::FETCH_ASSOC);
$total_documents = $results_total_documents['total_documents'];
// END GET TOTAL DOCUMENTS

// GET THE TOTAL UNIQUE PENDING REQUEST
$get_total_pending_request = "SELECT COUNT(DISTINCT request_number) AS total_pending_request 
                               FROM `tbl_document_request` 
                               WHERE status = 'pending'";
$stmt_total_pending_request = $conn->prepare($get_total_pending_request);
$stmt_total_pending_request->execute();
$results_total_pending_request = $stmt_total_pending_request->fetch(PDO::FETCH_ASSOC);
$total_pending_request = $results_total_pending_request['total_pending_request'];
// END GET TOTAL PENDING REQUEST


// GET THE TOTAL UNIQUE PAID REQUEST
$get_total_paid_request = "SELECT COUNT(DISTINCT request_number) AS total_paid_request 
                           FROM `tbl_document_request` 
                           WHERE status = 'paid'";
$stmt_total_paid_request = $conn->prepare($get_total_paid_request);
$stmt_total_paid_request->execute();
$results_total_paid_request = $stmt_total_paid_request->fetch(PDO::FETCH_ASSOC);
$total_paid_request = $results_total_paid_request['total_paid_request'];
// END GET TOTAL PAID REQUEST

// GET TOTAL PAID
$get_total_paid = "SELECT SUM(total_price) AS total_paid FROM `tbl_document_reports` WHERE status = 'paid' OR 'claimable' OR 'claimed'";
$stmt_total_paid = $conn->prepare($get_total_paid);
$stmt_total_paid->execute();
$results_total_paid = $stmt_total_paid->fetch(PDO::FETCH_ASSOC);
$total_paid = $results_total_paid['total_paid'];
// END GET TOTAL PRICE
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
                            <a href="dashboard.php" class="nav-link active">
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
                            <a href="manage_request.php" class="nav-link">
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
                            <h1 class="m-0 text-dark">DASHBOARD</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_cashier ?></h3>

                                    <p>Total Cashier</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-person-add"></i>
                                </div>
                                <a href="manage_cashier.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_documents ?></h3>

                                    <p>Total Documents</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="manage_documents" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <!-- ./col -->
                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_pending_request ?></h3>

                                    <p>Total Pending Request</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-clock"></i>
                                </div>
                                <a href="manage_request.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <!-- ./col -->
                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_paid_request ?></h3>

                                    <p>Total Paid Request</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-checkmark"></i>
                                </div>
                                <a href="manage_request.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3>₱<?php echo $total_paid ?></h3>

                                    <p>Reports</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-cash"></i>
                                </div>
                                <a href="reports.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
                    <!-- Main row -->
                    <div class="row">

                    </div>
                    <!-- /.row (main row) -->
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
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
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
</body>

</html>