<?php
session_start();
require 'database/connection.php';

$filter_school_year = isset($_GET['school_year']) ? $_GET['school_year'] : '';

$query = "SELECT DISTINCT school_year FROM tbl_deans_users_issuance ORDER BY school_year DESC";
$school_years_result = $conn->query($query);
$school_years = $school_years_result->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM tbl_deans_users_issuance";
if ($filter_school_year) {
    $query .= " WHERE school_year = :school_year";
}
$query .= " ORDER BY school_year DESC";
$stmt = $conn->prepare($query);

if ($filter_school_year) {
    $stmt->bindParam(':school_year', $filter_school_year);
}

$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <a class="dropdown-button active" href="#">SCHEDULES</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="class_schedules.php" id="">Class Schedules</a>
                        <a class="dropdown-link active" href="enrollment_schedules.php" id="">Enrollment Schedules</a>
                    </div>
                </div>
            </li>
            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button" href="#">e-Request</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="document_request.php" id="">Document Request</a>
                        <a class="dropdown-link" href="medical_request.php" id="">Medical Request</a>
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
    <div class="container mt-5">
        <div style="background-color: #001968; padding: 20px; color: white;" class="mb-5">
            <div class="text-center mb-4">
                <h1 class="font-weight-bold" style="color: white;">Enrollment Schedules</h1>
                <p>Enroll and apply now</p>
            </div>

            <form method="GET" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="schoolYearFilter">Filter by School Year:</label>
                        <select id="schoolYearFilter" name="school_year" class="form-control">
                            <option value="">All School Year</option>
                            <?php foreach ($school_years as $school_year) : ?>
                                <option value="<?= htmlspecialchars($school_year['school_year']) ?>" <?= $filter_school_year == $school_year['school_year'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($school_year['school_year']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php if ($schedules) : ?>
                    <div class="d-flex justify-content-end" style="gap: 5px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="enrollment_schedules.php" class="btn btn-secondary">Reset</a>
                    </div>
                <?php else : ?>
                <?php endif; ?>
            </form>
        </div>

        <!-- Display Schedules in a Table -->
        <div class="table-responsive mb-5">
            <table class="table table-striped">
                <thead style="background-color: #001968; color: white;">
                    <tr>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Schedule Upload</th>
                        <th>Uploaded At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($schedules) : ?>
                        <?php foreach ($schedules as $schedule) : ?>
                            <tr>
                                <td><?= htmlspecialchars($schedule['school_year']) ?></td>
                                <td><?= htmlspecialchars($schedule['semester']) ?></td>
                                <td><a class="btn btn-primary bg-blue" href="assets/uploads/enrollment_schedules/<?= htmlspecialchars($schedule['schedule_upload']) ?>" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        View Schedule</a></td>
                                <td><?= htmlspecialchars($schedule['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No schedules found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

</body>

</html>