<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'osds') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $requirements_id = $_GET['id'];
    $query = "SELECT * FROM `tbl_osds_post_requirements` WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $requirements_id, PDO::PARAM_INT);
    $stmt->execute();
    $posted_requirement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$posted_requirement) {
        echo "Requirements not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requirements_description = $_POST['requirements_description'];

    $requirements_upload = $posted_requirement['requirements_upload'];
    if (isset($_FILES['requirements_upload']) && $_FILES['requirements_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/posted_requirements/';
        $new_file_name = time() . '_' . basename($_FILES['requirements_upload']['name']);
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['requirements_upload']['tmp_name'], $upload_path)) {
            $requirements_upload = $new_file_name;
        } else {
            $_SESSION['error'] = 'Failed to upload file.';
            header('Location: edit_requirements.php');
            exit();
        }
    }

    $update_query = "UPDATE `tbl_osds_post_requirements` SET requirements_description = :requirements_description, requirements_upload = :requirements_upload, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':requirements_description', $requirements_description);
    $stmt->bindParam(':requirements_upload', $requirements_upload);
    $stmt->bindParam(':id', $requirements_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Requirements updated successfully!';
        header('Location: posted_requirements.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error updating requirements!';
        header('Location: posted_requirements.php');
        exit();
    }
}
?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GSU | e-Request</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../public/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="../public/plugins/toastr/toastr.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/dist/css/adminlte.min.css?v=3.2.0">
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

        .profile-box img {
            max-width: 100px;
            height: 100px;
            border-radius: 10px;
            margin-top: 10px;
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
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_osds.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage OSDS
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="posted_requirements.php" class="nav-link active">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>
                                    Posted Requirements
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
                                <li class="breadcrumb-item"><a href="posted_requirements.php">POSTED REQUIREMENTS</a></li>
                                <li class="breadcrumb-item active">EDIT REQUIREMENTS</li>
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
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-primary">
                                <div style="background-color: #001968 !important;" class="card-header">
                                    <h3 class="card-title" style="font-size: 25px;">EDIT REQUIREMENTS</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="" id="quickForm" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <!-- Hidden Field for Deans ID -->
                                        <input type="hidden" name="osds_id" class="form-control" value="<?php echo htmlspecialchars($admin_id); ?>">

                                        <div class="form-group">
                                            <label for="">Type of Requirements</label>
                                            <input type="text" class="form-control" name="requirements_description" id="requirements_description" value="<?php echo htmlspecialchars($posted_requirement['requirements_description']); ?>">
                                        </div>
                                        <!-- Schedule File Upload -->
                                        <div class="form-group">
                                            <label for="requirements_upload">Upload Schedule (PDF/Image)</label>
                                            <input type="file" id="requirements_upload" name="requirements_upload" class="form-control">
                                            <small>Current File: <?php echo htmlspecialchars($posted_requirement['requirements_upload']); ?></small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex" style="gap: 5px;">
                                        <button type="submit" class="btn btn-primary ml-auto">Update</button>
                                        <a href="posted_requirements.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!--/.col (left) -->
                        <!-- right column -->
                        <div class="col-md-6">

                        </div>
                        <!--/.col (right) -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../public/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr -->
    <script src="../public/plugins/toastr/toastr.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../public/plugins/sweetalert2/sweetalert2.min.js"></script>
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
    <!-- jquery-validation -->
    <script src="../public/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="../public/plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/dist/js/adminlte.min.js?v=3.2.0"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../public/dist/js/demo.js"></script>
    <!-- Page specific script -->

    <script>
        $(function() {
            $('#quickForm').validate({
                rules: {
                    requirements_description: {
                        required: true
                    },
                },
                messages: {
                    requirements_description: {
                        required: "Please input type of requirements"
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>

</body>

</html>