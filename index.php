<?php
session_start();
include('database/connection.php');
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/6934bb79c3.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .dropdown-button.active {
            background-color: #fff;
            color: #004080 !important;
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
                    <a class="dropdown-button active" href="index.php">HOME</a>
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
                    <a class="dropdown-button" href="e-request.php">e-Request</a>
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
    <div class="video-banner">
        <video autoplay muted loop playsinline class="banner-video">
            <source src="assets/images/stream-home.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="banner-content">
            <h1>Welcome to GSU | e-Request</h1>
            <p>Your gateway to efficient and accessible requests.</p>
        </div>
    </div>


    <section class="container my-5">
        <div class="row align-items-center">
            <!-- Right Column: Image -->
            <div class="col-md-6 text-center">
                <img src="assets/images/devices.jpg" class="img-fluid" style="border-radius: 20px; max-width: 100%; height: auto;" alt="" />
            </div>
            <!-- Left Column: Title and Description -->
            <div class="col-md-6">
                <h1 class="text-center" style="font-weight: 400; font-family: Itim, cursive; color: #001968;">
                    Request Documents Online
                </h1>
                <p style="text-align: justify; line-height: 2; font-size:18px;">
                    Experience a faster, more convenient way to request documents with our easy-to-use online application—streamlining
                    your process, saving time, and ensuring secure access to your needed files from anywhere.
                </p>
                <a class="btn btn-info bg-blue" href="e-request.php">
                    REQUEST NOW <i class="fa fa-sign-in" aria-hidden="true"></i>
                </a>
            </div>



        </div>
    </section>


    <!-- Card Section -->
    <div class="container mt-4">
        <div class="row">
            <!-- Card 1 -->
            <div class="col-md-4 mb-4">
                <div class="card p-3 d-flex flex-row  align-items-center" style="border: 2px solid #001968">
                    <i class="fa fa-sign-in" style="font-size: 2rem; margin-right: 15px; color:#001968;"></i>
                    <div>
                        <h5 class="card-title mb-1" style="text-align: left;">Sign Up</h5>
                        <p class="card-text" style="text-align: left;">Sign up with using your email address to request a document</p>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4 mb-4">
                <div class="card p-3 d-flex flex-row align-items-center" style="border: 2px solid #001968">
                    <i class="fa fa-question" style="font-size: 2rem; margin-right: 15px; color:#001968;"></i>
                    <div>
                        <h5 class="card-title mb-1" style="text-align: left;">Instructions</h5>
                        <p class="card-text" style="text-align: left;"> Know more about the request instructions </p>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4 mb-4">
                <div class="card p-3 d-flex flex-row align-items-center" style="border: 2px solid #001968">
                    <i class="fa fa-peso-sign" style="font-size: 2rem; margin-right: 15px; color:#001968;"></i>
                    <div>
                        <h5 class="card-title mb-1" style="text-align: left;">Payment</h5>
                        <p class="card-text" style="text-align: left;">Available Online Payments: <br> Gcash</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <section class="container my-5">
        <div class="row align-items-center">
            <!-- Right Column: Image -->
            <div class="col-md-6 text-center">
                <img src="assets/images/email.jpg" class="img-fluid" style="border-radius: 20px; max-width: 100%; height: 250px;" alt="" />
            </div>
            <!-- Left Column: Title and Description -->
            <div class="col-md-6">
                <h1 class="text-center" style="font-weight: 400; font-family: Itim, cursive; color:#001968;">
                    Sign Up
                </h1>
                <p style="text-align: justify; line-height: 2; font-size:18px;">
                    "Sign up easily with your valid email address
                    and start your journey with us. A confirmation
                    email will be sent to verify your account and
                    ensure secure access to our services.".
                </p>
            </div>
        </div>
    </section>


    <section class="container my-5">
        <div class="row align-items-center">

            <div class="col-md-6">
                <h1 class="text-center" style="font-weight: 400; font-family: Itim, cursive; color:#001968;">
                    Fill Up Form
                </h1>
                <p style="text-align: justify; line-height: 2; font-size:18px;">
                    Fill Up the Request Form with the Student
                    Requester details, select documents and
                    click submit. The registrar will check if the
                    student is eligible to take the document
                    requested.
                </p>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/images/form1.png" class="img-fluid" style="border-radius: 20px; max-width: 100%; height: 250px;" alt="" />
            </div>
        </div>
    </section>


    <section class="container my-5">
        <div class="row align-items-center">
            <!-- Right Column: Image -->
            <div class="col-md-6 text-center">
                <img src="assets/images/check.jpg" class="img-fluid" style="border-radius: 20px; max-width: 100%; height: 250px;" alt="" />
            </div>
            <!-- Left Column: Title and Description -->
            <div class="col-md-6">
                <h1 class="text-center" style="font-weight: 400; font-family: Itim, cursive; color:#001968;">
                    Check Status
                </h1>
                <p style="text-align: justify; line-height: 2; font-size:18px;">
                    Once the request is approved, you can
                    now pay and upload the payment reference.
                    Once the cashier approved the payment,
                    the status of the request will be on process by the registrar
                    and all you need to do is to wait until it is
                    done.

                </p>
            </div>
        </div>
    </section>


    <section class="container my-5">
        <div class="row align-items-center">

            <div class="col-md-6">
                <h1 class="text-center" style="font-weight: 400; font-family: Itim, cursive; color:#001968;">
                    For Pick Up / Completed
                </h1>
                <p style="text-align: justify; line-height: 2; font-size:18px;">
                    It may take 1-3 working days, some may
                    take 7 days. Check request status and once
                    it is completed or for pick up, you can now
                    get it at the schools registrar office!
                </p>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/images/completed.png" class="img-fluid" style="border-radius: 20px; max-width: 100%; height: 250px;" alt="" />
            </div>
        </div>
    </section>

    <!------- FOOTER ----->
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/home.js"></script>
</body>

</html>