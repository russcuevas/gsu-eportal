<?php
session_start();
require 'database/connection.php';


// READ DOCUMENTS
$get_documents = "SELECT * FROM `tbl_documents`";
$stmt_get_documents = $conn->query($get_documents);
$documents = $stmt_get_documents->fetchAll(PDO::FETCH_ASSOC);
// END READ DOCUMENTS


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $laboratory_request = 'requested';
        $with_med_cert = $_POST['with_med_cert'];
        $med_cert_picture = null;
        $status = 'pending';

        $request_number = rand(1, 10000000);

        $sql = "INSERT INTO tbl_clinic_request (user_id, laboratory_request, with_med_cert, med_cert_picture, status, request_number) 
                VALUES (:user_id, :laboratory_request, :with_med_cert, :med_cert_picture, :status, :request_number)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':laboratory_request', $laboratory_request);
        $stmt->bindParam(':with_med_cert', $with_med_cert);
        $stmt->bindParam(':med_cert_picture', $med_cert_picture);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':request_number', $request_number);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Medical appointment requested successfully. Please wait for the approval by the clinic nurse.";
            header("Location: medical_request.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to submit the request. Please try again.";
            header("Location: medical_request.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please log in first.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="Images/logo.png">
    <title>GSU | e-Request</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/6934bb79c3.js"></script>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
        }

        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item {
            margin: 0 5px;
        }

        .page-link {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            background-color: #00529B;
            border: 1px solid #004080;
            border-radius: 4px;
        }

        .page-item.active .page-link {
            background-color: #003366;
            color: white;
        }

        .page-link:hover {
            background-color: #004080;
        }

        .dropdown-button.active {
            background-color: #fff;
            color: #004080 !important;
        }

        .dropdown-link.active {
            background-color: #fff;
            color: #004080 !important;
            font-weight: 900 !important;
        }
    </style>
</head>

<body>
    <div id="myTopnav">
        <div class="logo">
            <a id="home" href="" class="logo-link"><img src="assets/images/gsu-logo.jpg" alt="Logo" style="width:50px; border-radius: 50px;"> &nbsp; <span style="color: white; font-weight: 900;">GSU | e-Request</span></a>
        </div>
        <a href="javascript:void(0);" class="show-nav" onclick="sideNav()"><i class="fa fa-bars"></i></a>
        <ul id="navbar" class="navbar-menu">
            <li class="navbar-item hide-nav" onclick="sideNav()">
                <a class="item-link" href="javascript:void(0);"><i class="fa fa-times"></i></a>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="index.php">HOME</a>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="requirements.php">REQUIREMENTS</a>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="#">SCHEDULES</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="class_schedules.php" id="">Class Schedules</a>
                        <a class="dropdown-link" href="enrollment_schedules.php" id="">Enrollment Schedules</a>
                    </div>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button active" href="#">e-Request</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="document_request.php" id="">Document Request</a>
                        <a class="dropdown-link active" href="medical_request.php" id="">Medical Request</a>
                    </div>
                </div>
            </li>

            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="item-link" href="#">e-Portal</a>
                    <div class="dropdown-content" style="width:250px">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="dropdown-link" href="student_portal/dashboard.php" id="">Student Portal</a>
                            <a class="dropdown-link" href="logout.php" id="">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-link" href="login.php" id="">Student Portal</a>
                            <a class="dropdown-link" href="admin_login.php" id="">Employee Portal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <!-- Page Content -->
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <form method="post" class="col-12 col-md-8 p-4 bg-light rounded" enctype="multipart/form-data">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="student_id" value="<?php echo $_SESSION['student_id']; ?>">
                    <input type="hidden" name="fullname" value="<?php echo $_SESSION['fullname']; ?>">
                    <input type="hidden" name="laboratory_request" value="requested">
                <?php else: ?>
                <?php endif; ?>
                <figure>
                    <blockquote class="blockquote">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <p>Please check the information of your account if it is correct.</p>
                        <?php else: ?>
                        <?php endif; ?>
                    </blockquote>

                </figure>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <div class="mb-4">
                        <h5>Your Information:</h5>
                        <div class="form-group">
                            <label for="">
                                <span>Student ID: <?php echo $_SESSION['student_id']; ?></span><br>
                                <span>Email: <?php echo $_SESSION['email']; ?></span><br>
                                <span>Fullname: <?php echo $_SESSION['fullname']; ?></span><br>
                                <span>Age: <?php echo $_SESSION['age']; ?></span><br>
                                <span>Year & Course: <?php echo $_SESSION['year']; ?> - <?php echo $_SESSION['course'] ?></span><br>
                                <span style="text-transform: capitalize;">Gender: <?php echo $_SESSION['gender']; ?></span><br>
                                <span style="text-transform: capitalize;">Status: <?php echo $_SESSION['status']; ?></span><br>
                            </label>
                            <span class="text-danger field-validation-valid" data-valmsg-for="" data-valmsg-replace="true"></span>
                        </div>
                    </div>
                <?php else: ?>
                <?php endif; ?>


                <section>
                    <div class="card-body">
                        <p class="text-muted">
                            <strong style="color: red">Medical Appointment Information:</strong><br>
                            The clinic is open from Monday to Friday, 8:00 AM to 5:00 PM<br><br><br>

                            <strong>You need a medical certificate?</strong><br>
                        <div>
                            <input type="radio" id="yes" name="with_med_cert" value="Yes">
                            <label for="yes">Yes</label>
                        </div>
                        <div>
                            <input type="radio" id="no" name="with_med_cert" value="No">
                            <label for="no">No</label>
                        </div>
                        </p>


                    </div>

                    <div class="text-end float-right">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button type="submit" class="btn btn-primary bg-blue mt-3">
                                <i class="fas fa-paper-plane"></i> REQUEST FOR LABORATORY
                            </button>
                        <?php else: ?>
                            <br>
                            <a href="login.php" class="btn btn-danger">LOGIN FIRST TO REQUEST</a>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5">
                        <p class="text-muted">
                            <strong style="color: red">Instructions:</strong><br>
                            <strong>Please wait for the appointed date by the clinic nurse</strong><br>
                            <strong>Check your status here <a href="student_portal/my_request_medical.php">CLICK HERE</a></strong>

                        </p>
                    </div>
                </section>

            </form>
        </div>
    </div>
    <!-- Reuse Footer -->
    <div id="footer" class="footer pt-3 pb-2" style="background-color: #001968">
        <div class="container">
            <div class="text-white">
                <div class="row">
                    <div class="col-md-8">
                        <p style="font-family: CENTURY GOTHIC; font-size: 10px;">
                        <table style="width: 100%; font-family: CENTURY GOTHIC; font-size: 10px; ">
                            <tr valign="top">
                                <td style="text-align: center;">
                                    <i class="fas fa-map-marker-alt" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td>
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        Mclain,<br style="margin-top: 0px; margin-bottom: 0px">
                                        Buenavista,<br style="margin-top: 0px; margin-bottom: 0px">
                                        Guimaras<br style="margin-top: 0px; margin-bottom: 0px">
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="text-align: center;">
                                    <i class="fa fa-phone" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td>
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        (033) 580 8244<br style="margin-top: 0px; margin-bottom: 0px">
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-top: 10px; text-align: center;">
                                    <i class="fab fa-google" style="font-size:16px; color: #fff"></i>
                                </td>
                                <td style="padding-top: 8px;">
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        university.president@gsu.edu.ph
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-top: 10px; text-align: center;">
                                    <i class='fab fa-facebook-f' style='font-size:16px; color: #fff'></i>
                                </td>
                                <td style="padding-top: 8px; ">
                                    <font style="font-family: CENTURY GOTHIC; font-size: 14px; margin-top: 20px;">
                                        <a href="https://www.facebook.com/GuimarasStateUniversity" style="text-decoration: none; color: white">facebook.com/GuimarasStateUniversity</a>
                                        <!--Messenger Link-->
                                        <!--m.me/WeFormHearts-->
                                    </font>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td style="padding-right: 20px; text-align: center;">

                                </td>
                                <td style="padding-top: 40px">
                                    <a class="btn btn-link text-white" href="" id="loginpage" style="font-size: 10px">© Copyright 2024. All Rights Reserved.</a>
                                </td>
                            </tr>
                        </table>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '" . $_SESSION['error'] . "',
        });
    </script>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['warning'])) {
        echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: '" . $_SESSION['warning'] . "',
        });
    </script>";
        unset($_SESSION['warning']);
    }

    if (isset($_SESSION['success'])) {
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '" . $_SESSION['success'] . "',
        });
    </script>";
        unset($_SESSION['success']);
    }
    ?>
</body>

</html>