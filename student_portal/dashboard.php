<?php
include '../database/connection.php';

//session
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:../login.php');
}

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;


$get_total_doc_request = "
    SELECT COUNT(DISTINCT request_number) AS total_doc_request 
    FROM `tbl_document_request` 
    WHERE user_id = :user_id AND status = 'pending'";
$stmt_total_doc_request = $conn->prepare($get_total_doc_request);
$stmt_total_doc_request->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_total_doc_request->execute();
$results_total_doc_request = $stmt_total_doc_request->fetch(PDO::FETCH_ASSOC);
$total_doc_request = $results_total_doc_request['total_doc_request'];


$get_my_paid_document = "
    SELECT COUNT(DISTINCT request_number) AS my_paid_document 
    FROM `tbl_document_request` 
    WHERE user_id = :user_id AND status = 'paid'";
$stmt_my_paid_document = $conn->prepare($get_my_paid_document);
$stmt_my_paid_document->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_my_paid_document->execute();
$results_my_paid_document = $stmt_my_paid_document->fetch(PDO::FETCH_ASSOC);
$my_paid_document = $results_my_paid_document['my_paid_document'];


$get_claimable_document = "
    SELECT COUNT(DISTINCT request_number) AS claimable_document 
    FROM `tbl_document_request` 
    WHERE user_id = :user_id AND status = 'claimable'";
$stmt_claimable_document = $conn->prepare($get_claimable_document);
$stmt_claimable_document->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_claimable_document->execute();
$results_claimable_document = $stmt_claimable_document->fetch(PDO::FETCH_ASSOC);
$claimable_document = $results_claimable_document['claimable_document'];





$get_total_med_request = "SELECT COUNT(*) AS total_med_request FROM `tbl_clinic_request` WHERE user_id = :user_id AND status = 'pending'";
$stmt_total_med_request = $conn->prepare($get_total_med_request);
$stmt_total_med_request->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_total_med_request->execute();
$results_total_med_request = $stmt_total_med_request->fetch(PDO::FETCH_ASSOC);
$total_med_request = $results_total_med_request['total_med_request'];


$get_total_med_accept = "SELECT COUNT(*) AS total_med_accept FROM `tbl_clinic_request` WHERE user_id = :user_id AND status = 'accepted'";
$stmt_total_med_accept = $conn->prepare($get_total_med_accept);
$stmt_total_med_accept->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_total_med_accept->execute();
$results_total_med_accept = $stmt_total_med_accept->fetch(PDO::FETCH_ASSOC);
$total_med_accept = $results_total_med_accept['total_med_accept'];


$get_total_med_completed = "SELECT COUNT(*) AS total_med_completed FROM `tbl_clinic_request` WHERE user_id = :user_id AND status = 'completed'";
$stmt_total_med_completed = $conn->prepare($get_total_med_completed);
$stmt_total_med_completed->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_total_med_completed->execute();
$results_total_med_completed = $stmt_total_med_completed->fetch(PDO::FETCH_ASSOC);
$total_med_completed = $results_total_med_completed['total_med_completed'];
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
            <a style="background-color: #001968 !important; border-right: 1px solid #FCC737; border-bottom: 1px solid #FCC737;" href="dashboard.php" class="brand-link">
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
                            <a href="dashboard.php" class="nav-link active">
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
                            <a href="my_request_medical.php" class="nav-link">
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
                                    <h3><?php echo $total_doc_request ?></h3>

                                    <p>Document Request</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_documents.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $my_paid_document ?></h3>

                                    <p>Paid Documents</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_documents.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $claimable_document ?></h3>

                                    <p>Claimable Documents</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_documents.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_med_request ?></h3>

                                    <p>Medical Request</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_medical.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_med_accept ?></h3>

                                    <p>My Appointment</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_medical.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <!-- small box -->
                            <div style="background-color: #001968 !important;" class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $total_med_completed ?></h3>

                                    <p>Completed Medical</p>
                                </div>
                                <div class="icon">
                                    <i style="color: white !important;" class="ion ion-folder"></i>
                                </div>
                                <a href="my_request_medical.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
    <script src="../public/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="../public/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="../public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="../public/plugins/chart.js/Chart.min.js"></script>
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
</body>

</html>