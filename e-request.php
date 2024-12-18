<?php
session_start();
require 'database/connection.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

// READ DOCUMENTS
$get_documents = "SELECT * FROM `tbl_documents`";
$stmt_get_documents = $conn->query($get_documents);
$documents = $stmt_get_documents->fetchAll(PDO::FETCH_ASSOC);
// END READ DOCUMENTS
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
                    <a class="dropdown-button" href="#">SCHEDULES</a>
                    <div class="dropdown-content" style="width:300px">
                        <a class="dropdown-link" href="class_schedules.php" id="">Class Schedules</a>
                        <a class="dropdown-link" href="enrollment_schedules.php" id="">Enrollment Schedules</a>
                    </div>
                </div>
            </li>
            <li class="navbar-item">
                <div class="dropdown-div">
                    <a class="dropdown-button active" href="e-request.php">e-Request</a>
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
            <form method="post" class="col-12 col-md-8 p-4 bg-light rounded">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <input type="text" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <?php endif; ?>
                <figure>
                    <blockquote class="blockquote">
                        <p>Please check the fields of your account if is correct</p>
                    </blockquote>
                </figure>

                <div class="mb-4">
                    <h5>Student's Information:</h5>
                    <div class="form-group">
                        <label for="StudentFullName">
                            <span class="text-danger">*</span> Student's Name (Last Name, First Name, Middle)
                        </label>
                        <input class="form-control" type="text" data-val="true" data-val-required="Student full name is required." id="StudentFullName" name="StudentFullName" value="" />
                        <span class="text-danger field-validation-valid" data-valmsg-for="StudentFullName" data-valmsg-replace="true"></span>
                    </div>
                    <div class="form-group">
                        <label for="YearLevel">
                            <span class="text-danger">*</span> Student Year Level
                        </label>
                        <select class="form-control" data-val="true" data-val-required="The YearLevel field is required." id="YearLevel" name="YearLevel">
                            <option value="">Select Year Level</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Senior High">Senior High</option>
                            <option value="College">College</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Requestor's Information:</h5>
                    <div class="form-group">
                        <label for="RequestorFullName">
                            <span class="text-danger">*</span> Requestor's Full Name
                        </label>
                        <input class="form-control" type="text" data-val="true" data-val-required="The RequestorFullName field is required." id="RequestorFullName" name="RequestorFullName" value="" />
                    </div>
                    <div class="form-group">
                        <label for="Email">
                            <span class="text-danger">*</span> Requestor's Email
                        </label>
                        <input class="form-control" type="text" data-val="true" data-val-required="The Email field is required." id="Email" name="Email" value="" />
                    </div>
                </div>

                <section>
                    <h5>Select Document:</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Document Type</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $document): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="Documents[0].IsSelected" value="true"
                                                class="document-selection"
                                                data-price="50.00"
                                                data-index="0" />
                                            <input type="hidden" name="Documents[0].DocumentId" value="3" />
                                        </td>
                                        <td><?php echo $document['type_of_documents'] ?></td>
                                        <td>₱<?php echo $document['price'] ?></td>
                                        <td>
                                            <input type="number" name="" value="1"
                                                class="form-control" />
                                        </td>
                                        <td class="document-total">0.00</td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end float-right">
                        <strong>Prepare a Total Amount of:</strong>
                        <span id="totalAmount">0.00</span>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> SUBMIT
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-danger">LOGIN FIRST TO REQUEST</a>
                        <?php endif; ?>
                    </div>
                    <div class="mt-5">
                        <p class="text-muted">
                            <strong>Your request will be submitted for validation.</strong>
                            <strong><br>The cashier will check if you are paid in your request based on your uploaded payment proof</strong><br>
                            <strong>Once your payment proof is approved by the cashier <br> System will give you a receipt and you can get your requested document in the registrar showing receipt</strong>
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

</body>

</html>