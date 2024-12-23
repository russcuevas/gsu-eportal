<?php
include '../database/connection.php';

session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:../admin_login.php');
    exit();
}

if ($_SESSION['role'] !== 'cashier') {
    header('location:../admin_login.php');
    exit();
}

// Fetch document details if 'id' is passed in the URL
if (isset($_GET['id'])) {
    $documents_id = $_GET['id'];
    $query = "SELECT * FROM `tbl_documents` WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $documents_id, PDO::PARAM_INT);
    $stmt->execute();
    $documents = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$documents) {
        echo "Document not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_of_documents = $_POST['type_of_documents'];
    $price = $_POST['price'];

    $query = "UPDATE tbl_documents SET type_of_documents = :type_of_documents, price = :price, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':type_of_documents', $type_of_documents);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':id', $documents_id, PDO::PARAM_INT);

    $execute_result = $stmt->execute();

    if ($execute_result) {
        $_SESSION['success'] = 'Document updated successfully!';
        header('Location: manage_documents.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error updating document.';
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
                            <a href="manage_documents.php" class="nav-link active">
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

                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">DASHBOARD</a></li>
                                <li class="breadcrumb-item"><a href="manage_documents.php">MANAGE DOCUMENTS</a></li>
                                <li class="breadcrumb-item active">EDIT DOCUMENTS</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">EDIT DOCUMENTS</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="" id="quickForm" method="POST">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Type of documents</label>
                                            <select class="form-control" id="type_of_documents" name="type_of_documents">
                                                <option value="">Select Documents</option>
                                                <option value="RF" <?php echo ($documents['type_of_documents'] == 'RF') ? 'selected' : ''; ?>>RF</option>
                                                <option value="GRADES" <?php echo ($documents['type_of_documents'] == 'GRADES') ? 'selected' : ''; ?>>GRADES</option>
                                                <option value="TOR" <?php echo ($documents['type_of_documents'] == 'TOR') ? 'selected' : ''; ?>>Transcript of Records</option>
                                                <option value="CAV-G" <?php echo ($documents['type_of_documents'] == 'CAV-G') ? 'selected' : ''; ?>>CAV - Graduates</option>
                                                <option value="CAV-UG" <?php echo ($documents['type_of_documents'] == 'CAV-UG') ? 'selected' : ''; ?>>CAV - Undergraduates</option>
                                                <option value="Permit to cross enroll" <?php echo ($documents['type_of_documents'] == 'Permit to cross enroll') ? 'selected' : ''; ?>>Permit to cross enroll</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" class="form-control" id="price" value="<?php echo $documents['price']; ?>">
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex" style="gap: 5px;">
                                        <button type="submit" class="btn btn-primary ml-auto">Update</button>
                                        <a href="manage_documents.php" class="btn btn-secondary">Cancel</a>
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
                    type_of_documents: {
                        required: true
                    },
                    price: {
                        required: true,
                        number: true,
                        pattern: /^[0-9]+(\.[0-9]{1,2})?$/
                    }
                },
                messages: {
                    type_of_documents: {
                        required: "Please select type of requirements"
                    },
                    price: {
                        required: "Please enter a price",
                        number: "Please enter a valid number",
                        pattern: "Please enter a valid number (e.g., 10 or 10.02)"
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