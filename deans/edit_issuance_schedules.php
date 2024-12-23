<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'deans') {
    header('location:../admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $issuance_id = $_GET['id'];
    $query = "SELECT * FROM `tbl_deans_users_issuance` WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $issuance_id, PDO::PARAM_INT);
    $stmt->execute();
    $enrollment_schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$enrollment_schedule) {
        echo "Schedule not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_year = $_POST['school_year'];
    $semester = $_POST['semester'];

    $schedule_upload = $enrollment_schedule['schedule_upload'];
    if (isset($_FILES['schedule_upload']) && $_FILES['schedule_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/enrollment_schedules/';
        $new_file_name = time() . '_' . basename($_FILES['schedule_upload']['name']);
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['schedule_upload']['tmp_name'], $upload_path)) {
            $schedule_upload = $new_file_name;
        } else {
            $_SESSION['error'] = 'Failed to upload file.';
            header('Location: edit_issuance_schedules.php');
            exit();
        }
    }

    $update_query = "UPDATE `tbl_deans_users_issuance` SET school_year = :school_year, semester = :semester, schedule_upload = :schedule_upload, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':school_year', $school_year);
    $stmt->bindParam(':semester', $semester);
    $stmt->bindParam(':schedule_upload', $schedule_upload);
    $stmt->bindParam(':id', $issuance_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Schedule updated successfully!';
        header('Location: enrollment_schedules.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error updating schedule!';
        header('Location: enrollment_schedules.php');
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
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
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
                            <a href="manage_deans.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Deans
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="enrollment_schedules.php" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Class Schedules
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="enrollment_schedules.php" class="nav-link active">
                                <i class="nav-icon far fa-calendar-alt"></i>
                                <p>
                                    Enrollment Schedules
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
                                <li class="breadcrumb-item"><a href="enrollment_schedules.php">ENROLLMENT SCHEDULES</a></li>
                                <li class="breadcrumb-item active">EDIT ISSUANCE SCHEDULES</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">EDIT ISSUANCE SCHEDULES</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="" id="quickForm" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <!-- Hidden Field for Deans ID -->
                                        <input type="hidden" name="deans_id" class="form-control" value="<?php echo htmlspecialchars($admin_id); ?>">

                                        <!-- School Year -->
                                        <div class="form-group">
                                            <label>School Year</label>
                                            <select class="form-control" id="school_year" name="school_year" required>
                                                <option value="">Select School Year</option>
                                                <option value="2023-2024" <?php echo $enrollment_schedule['school_year'] === '2023-2024' ? 'selected' : ''; ?>>2023-2024</option>
                                                <option value="2024-2025" <?php echo $enrollment_schedule['school_year'] === '2024-2025' ? 'selected' : ''; ?>>2024-2025</option>
                                                <option value="2025-2026" <?php echo $enrollment_schedule['school_year'] === '2025-2026' ? 'selected' : ''; ?>>2025-2026</option>
                                                <option value="2026-2027" <?php echo $enrollment_schedule['school_year'] === '2026-2027' ? 'selected' : ''; ?>>2026-2027</option>
                                            </select>
                                        </div>

                                        <!-- Semester -->
                                        <div class="form-group">
                                            <label>Semester</label>
                                            <select class="form-control" id="semester" name="semester" required>
                                                <option value="">Select Semester</option>
                                                <option value="1st semester" <?php echo $enrollment_schedule['semester'] === '1st semester' ? 'selected' : ''; ?>>1st Semester</option>
                                                <option value="2nd semester" <?php echo $enrollment_schedule['semester'] === '2nd semester' ? 'selected' : ''; ?>>2nd Semester</option>
                                            </select>
                                        </div>


                                        <!-- Schedule File Upload -->
                                        <div class="form-group">
                                            <label for="schedule_upload">Upload Schedule (PDF/Image)</label>
                                            <input type="file" id="schedule_upload" name="schedule_upload" class="form-control">
                                            <small>Current File: <?php echo htmlspecialchars($enrollment_schedule['schedule_upload']); ?></small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex" style="gap: 5px;">
                                        <button type="submit" class="btn btn-primary ml-auto">Update</button>
                                        <a href="enrollment_schedules.php" class="btn btn-secondary">Cancel</a>
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
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
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
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js?v=3.2.0"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- Page specific script -->

    <script>
        $(function() {
            $('#quickForm').validate({
                rules: {
                    school_year: {
                        required: true
                    },
                    semester: {
                        required: true
                    },
                },
                messages: {
                    school_year: {
                        required: "Please select a school year"
                    },
                    semester: {
                        required: "Please select a semester"
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