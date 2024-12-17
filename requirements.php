<?php
require 'database/connection.php';

$query = "SELECT * FROM tbl_osds_post_requirements";
$stmt = $conn->prepare($query);
$stmt->execute();
$requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <style>
        .card-header {
            background-color: #0056b3;
            border-radius: 0.25rem;
        }

        .btn-link {
            color: #fff;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 20px;
            font-size: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        .card {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
        }

        .card-header .btn-link:hover {
            color: #ffc107;
        }

        .btn-info {
            background-color: #007bff;
            border: none;
            padding: 5px 10px;
        }

        .btn-info:hover {
            background-color: #0056b3;
        }

        .card-body {
            padding-left: 20px;
            padding-right: 20px;
        }

        .card-body p {
            color: black;
            text-align: left;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .card-body .btn-info {
            margin-top: 5px;
            text-align: left;
        }

        .card-header .fas {
            font-size: 1.2rem;
        }

        .container {
            max-width: 1200px;
        }

        .toggle-caret {
            transition: transform 0.3s ease;
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
                    <a class="dropdown-button active" href="requirements.php">REQUIREMENTS</a>
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
                    <a class="item-link" href="#">e-Request</a>
                    <div class="dropdown-content" style="width:250px">
                        <a class="dropdown-link" href="login.php" id="">Student Portal</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-link" href="admin_login.php" id="">Employee Portal</a>
                    </div>
                </div>
            </li>
            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="item-link" href="#">e-Portal</a>
                    <div class="dropdown-content" style="width:250px">
                        <a class="dropdown-link" href="login.php" id="">Student Portal</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-link" href="admin_login.php" id="">Employee Portal</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <!-- Page Content -->
    <div class="container mt-5">
        <div style="background-color: #001968; padding: 20px; color: white;" class="mb-5 rounded-lg shadow-lg">
            <div class="text-center mb-4">
                <h1 class="font-weight-bold text-white">Posted Requirements</h1>
                <p>List of the requirements being posted by the OSDS</p>
            </div>

            <div id="accordion">
                <?php foreach ($requirements as $index => $requirement): ?>
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-header" style="background-color: white;" id="heading<?php echo $index; ?>">
                            <h5 class="mb-0 accordion-h5">
                                <button class="btn btn-link font-weight-bold" type="button" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="true" aria-controls="collapse<?php echo $index; ?>" style="text-decoration: none; font-size: 1.1rem; color: #001968; text-transform: uppercase;">
                                    <i class="fas fa-caret-right mr-2 toggle-caret"></i><?php echo htmlspecialchars($requirement['requirements_description']); ?>
                                </button>
                            </h5>
                        </div>

                        <div id="collapse<?php echo $index; ?>" class="collapse" aria-labelledby="heading<?php echo $index; ?>" data-parent="#accordion">
                            <div class="card-body bg-light">
                                <div class="mb-3">
                                    <p><strong>UPLOADED FILE:</strong>
                                        <?php if ($requirement['requirements_upload']): ?>
                                            <a href="assets/uploads/posted_requirements/<?php echo htmlspecialchars($requirement['requirements_upload']); ?>" target="_blank" class="btn btn-primary bg-blue"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                VIEW PDF</a>
                                        <?php else: ?>
                                            <span class="text-muted">No file uploaded.</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div>
                                    <p><strong>UPLOADED AT:</strong>
                                        <?php
                                        $createdAt = new DateTime($requirement['created_at']);
                                        echo $createdAt->format('F j, Y, g:i a');
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>


                    </div>
                <?php endforeach; ?>
            </div>
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
    <script>
        $(document).ready(function() {
            $('#accordion').on('show.bs.collapse hide.bs.collapse', function(e) {
                var target = $(e.target);
                var button = target.prev('.card-header').find('.toggle-caret');
                if (e.type === 'show') {
                    button.removeClass('fa-caret-right').addClass('fa-caret-down');
                } else {
                    button.removeClass('fa-caret-down').addClass('fa-caret-right');
                }
            });
        });
    </script>
</body>

</html>