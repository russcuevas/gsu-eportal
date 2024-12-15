<?php
include '../database/connection.php';

//session
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
}

// if not osds role it will redirect to login
if ($_SESSION['role'] !== 'osds') {
    header('location:../admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = sha1($_POST['password']);
    $role = $_POST['role'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $profile_image = $_FILES['profile_image'];
        $original_name = basename($profile_image['name']);
        $target_dir = "images/profile/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $original_name;
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);

        while (file_exists($target_file)) {
            $original_name = pathinfo($original_name, PATHINFO_FILENAME) . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $original_name;
        }

        if (move_uploaded_file($profile_image['tmp_name'], $target_file)) {
            $image_name = $original_name;
        } else {
            $_SESSION['error'] = 'Failed to upload the image.';
            header('Location: add_osds.php');
            exit();
        }
    } else {
        $image_name = 'default.jpg';
    }


    $query = "SELECT COUNT(*) FROM tbl_admin WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $email_exists = $stmt->fetchColumn();

    if ($email_exists > 0) {
        $_SESSION['error'] = 'Email address already exists.';
        header('Location: add_osds.php');
        exit();
    }

    $query = "INSERT INTO tbl_admin (fullname, email, password, role, profile_image) 
              VALUES (:fullname, :email, :password, :role, :profile_image)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':profile_image', $image_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Osds added successfully!';
        header('Location: add_osds.php');
        exit();
    } else {
        $_SESSION['error'] = 'There was an error adding the Osds.';
        header('Location: add_osds.php');
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
                            <a href="manage_osds.php" class="nav-link active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage OSDS
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="posted_requirements.php" class="nav-link">
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
                                <li class="breadcrumb-item"><a href="manage_osds.php">MANAGE OSDS</a></li>
                                <li class="breadcrumb-item active">ADD NEW OSDS</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">ADD NEW OSDS</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="quickForm" method="POST" action="" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="profile-box">
                                            <img id="profileImagePreview" src="#" alt="Profile Image" style="display: none; max-width: 100px; height: 100px;" />
                                        </div>

                                        <div class="form-group">
                                            <label for="profileImage">Profile Image</label>
                                            <input type="file" name="profile_image" class="form-control" id="profileImage">
                                        </div>

                                        <!-- Full Name -->
                                        <div class="form-group">
                                            <label for="fullname">Full Name</label>
                                            <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Enter Full Name">
                                        </div>

                                        <!-- Email Address -->
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email address</label>
                                            <input type="email" name="email" class="form-control <?php echo isset($_SESSION['error']) && strpos($_SESSION['error'], 'Email address already exists') !== false ? 'is-invalid' : ''; ?>" id="exampleInputEmail1" placeholder="Enter email" value="<?php echo isset($email) ? $email : ''; ?>">
                                            <?php if (isset($_SESSION['error']) && strpos($_SESSION['error'], 'Email address already exists') !== false): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo $_SESSION['error'];
                                                    unset($_SESSION['error']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                                        </div>

                                        <!-- Hidden Role Field -->
                                        <input type="hidden" name="role" value="osds">

                                    </div>
                                    <!-- /.card-body -->

                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex">
                                        <button type="submit" class="btn btn-primary ml-auto">Submit</button>
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
        // JavaScript to handle the image file input and display it in the profile box
        $(document).ready(function() {
            $("#profileImage").change(function(event) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Display the selected image
                    $('#profileImagePreview').attr('src', e.target.result).show();
                };
                // Read the selected file
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>

    <script>
        $(function() {
            $('#quickForm').validate({
                rules: {
                    profile_image: {
                        extension: "jpg|jpeg|png|gif"
                    },
                    fullname: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    profile_image: {
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed"
                    },
                    fullname: {
                        required: "Please enter your full name",
                        minlength: "Full name must be at least 3 characters long"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    }
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