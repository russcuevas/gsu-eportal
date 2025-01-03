<?php
include '../database/connection.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
    WHERE r.status = 'Accepted'
    ORDER BY r.requested_at DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit request
if (isset($_POST['update_button'])) {
    $request_id = $_POST['request_id'];
    $laboratory_request = $_POST['laboratory_request'];
    $status = $_POST['status'];
    $med_cert_picture = '';

    // Handle file upload for medical certificate
    if (isset($_FILES['med_cert_picture']) && $_FILES['med_cert_picture']['error'] == 0) {
        $upload_dir = '../assets/uploads/medical_certificate/';
        $file_name = $_FILES['med_cert_picture']['name'];
        $file_tmp = $_FILES['med_cert_picture']['tmp_name'];
        $file_type = $_FILES['med_cert_picture']['type'];

        $new_file_name = uniqid() . "_" . basename($file_name);
        $file_path = $upload_dir . $new_file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = 'Invalid file type. Only images (JPEG, PNG) and PDFs are allowed.';
            header("Location: accepted_request.php");
            exit();
        }

        if (move_uploaded_file($file_tmp, $file_path)) {
            $med_cert_picture = $new_file_name;
        } else {
            $_SESSION['error'] = 'Failed to upload the medical certificate.';
            header("Location: accepted_request.php");
            exit();
        }
    }

    // Update status to "Completed"
    if ($status == 'Completed') {
        $update_query = "
        UPDATE tbl_clinic_request 
        SET 
            laboratory_request = :laboratory_request, 
            status = :status,
            med_cert_picture = :med_cert_picture
        WHERE id = :request_id
    ";

        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':laboratory_request', $laboratory_request);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':med_cert_picture', $med_cert_picture);
        $stmt->bindParam(':request_id', $request_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Appointment completed successfully.';

            // Fetch user email and fullname
            $user_query = "SELECT email, fullname FROM tbl_users WHERE id = (SELECT user_id FROM tbl_clinic_request WHERE id = :request_id)";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bindParam(':request_id', $request_id);
            $user_stmt->execute();
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

            $user_email = $user['email'];
            $user_fullname = $user['fullname'];

            // Send confirmation email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'guimarasrequestingsystem@gmail.com';
                $mail->Password = 'idyztzjuzwcrupwp';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('gsu-erequest@gmail.com', 'Guimaras State University Clinic');
                $mail->addAddress($user_email, $user_fullname);

                $mail->isHTML(true);
                $mail->Subject = 'Your Clinic Appointment Has Been Completed';
                $mail->Body    = "<p>Dear $user_fullname,</p>
                              <p>Your clinic appointment has been successfully completed.</p>
                              <p>If you have any questions, feel free to contact us.</p>
                              <p>Best regards,<br>Clinic Staff</p>";

                $mail->send();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
                header("Location: accepted_request.php");
                exit();
            }

            header("Location: accepted_request.php");
            exit();
        } else {
            $_SESSION['error'] = 'Failed to update the appointment to Completed.';
        }
    } elseif ($status == 'Cancel') {
        $user_query = "SELECT email, fullname FROM tbl_users WHERE id = (SELECT user_id FROM tbl_clinic_request WHERE id = :request_id)";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bindParam(
            ':request_id',
            $request_id
        );
        $user_stmt->execute();
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        $user_email = $user['email'];
        $user_fullname = $user['fullname'];
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'guimarasrequestingsystem@gmail.com';
            $mail->Password = 'idyztzjuzwcrupwp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('gsu-erequest@gmail.com', 'Guimaras State University Clinic');
            $mail->addAddress($user_email, $user_fullname);

            $mail->isHTML(true);
            $mail->Subject = 'Your Clinic Appointment Has Been Cancelled';
            $mail->Body    = "<p>Dear $user_fullname,</p>
                          <p>Your clinic appointment has been cancelled as requested.</p>
                          <p>If you have any further questions, feel free to contact us.</p>
                          <p>Best regards,<br>Clinic Staff</p>";

            $mail->send();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
            header("Location: accepted_request.php");
            exit();
        }

        $delete_query = "
            DELETE FROM tbl_clinic_request 
            WHERE id = :request_id
        ";

        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':request_id', $request_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Appointment cancelled successfully.';
        } else {
            $_SESSION['error'] = 'Failed to cancel and delete the appointment.';
        }

        header("Location: accepted_request.php");
        exit();
    }
}
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
                            <a href="accepted_request.php" class="nav-link active">
                                <i class="nav-icon fas fa-check"></i>
                                <p>
                                    Accepted Appointment
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link">
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
                                <li class="breadcrumb-item active">APPOINTMENT</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">APPOINTMENT</h3>
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
                                                                    <form id="clinic-request-form" action="" method="POST" enctype="multipart/form-data">
                                                                        <input type="hidden" id="request_id" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                                        <input type="hidden" class="form-control" id="request_number" name="request_number" value="<?php echo $request['request_number']; ?>" readonly>
                                                                        <strong>Fullname:</strong> <?php echo $request['fullname']; ?><br>
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
                                                                        <input type="datetime-local"
                                                                            id="appointed_at_<?php echo $request['request_id']; ?>"
                                                                            value="<?php echo date('Y-m-d\TH:i', strtotime($request['appointed_at'])); ?>"
                                                                            onchange="updateAppointedAt(<?php echo $request['request_id']; ?>)"> <?php if ($request['with_med_cert'] === 'Yes'): ?><span style="font-size: 11px;">CLICK ICON TO EDIT DATETIME</span><br><br>
                                                                            <div class="form-group">
                                                                                <label for="med_cert_picture">Upload Medical Certificate</label>
                                                                                <input type="file" class="form-control" name="med_cert_picture" id="med_cert_picture">
                                                                            </div>
                                                                        <?php else: ?>
                                                                        <?php endif ?>

                                                                        <div class="form-group">
                                                                            <label for="laboratory_request">Laboratory Request</label>
                                                                            <select class="form-control" id="laboratory_request" name="laboratory_request" required>
                                                                                <option value="Completed" <?php echo ($request['laboratory_request'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="status">Status</label>
                                                                            <select class="form-control" id="status" name="status" required>
                                                                                <option value="Completed" <?php echo ($request['request_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                                                <option value="Cancel" <?php echo ($request['request_status'] == 'Cancel') ? 'selected' : ''; ?>>Cancel</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="d-flex justify-content-end" style="gap: 3px !important;">
                                                                            <button type="submit" name="update_button" class="btn btn-primary">UPDATE CHANGES</button>
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
            const appointmentForm = document.getElementById('clinic-request-form');
            appointmentForm.addEventListener('submit', function(event) {
                const requiredFields = document.querySelectorAll('[required]');
                let formValid = true;

                requiredFields.forEach(function(field) {
                    if (field.value.trim() === '') {
                        formValid = false;
                        field.style.borderColor = 'red';
                    } else {
                        field.style.borderColor = '';
                    }
                });

                if (formValid) {
                    // Open HoldOn spinner before submitting the form
                    HoldOn.open({
                        theme: "sk-bounce",
                        message: "Submitting your appointment...",
                        backgroundColor: "rgba(0, 0, 0, 0.7)",
                        textColor: "white",
                        spinnerColor: "#fff"
                    });
                } else {
                    // Prevent form submission if validation fails
                    event.preventDefault();
                }
            });
        });
    </script>

    <!-- update appointed at -->
    <script>
        function updateAppointedAt(requestId) {
            const appointedAtInput = document.getElementById(`appointed_at_${requestId}`);
            const newAppointedAt = appointedAtInput.value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_appointed_at.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                alert('Appointment date and time updated successfully.');
                            } else {
                                alert('Failed to update the appointment: ' + response.message);
                            }
                        } catch (e) {
                            console.error('Invalid JSON response:', xhr.responseText);
                            alert('An error occurred. Please try again.');
                        }
                    } else {
                        alert('Request failed with status: ' + xhr.status);
                    }
                }
            };

            xhr.send(`request_id=${requestId}&appointed_at=${newAppointedAt}`);
        }
    </script>



</body>

</html>