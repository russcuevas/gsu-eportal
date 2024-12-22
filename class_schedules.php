<?php
session_start();
require 'database/connection.php';

$items_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$departments_query = $conn->prepare("SELECT DISTINCT department FROM tbl_deans_post_class_schedules");
$departments_query->execute();
$departments = $departments_query->fetchAll(PDO::FETCH_ASSOC);

$years_query = $conn->prepare("SELECT DISTINCT year FROM tbl_deans_post_class_schedules ORDER BY year");
$years_query->execute();
$years = $years_query->fetchAll(PDO::FETCH_ASSOC);

$school_years_query = $conn->prepare("SELECT DISTINCT school_year FROM tbl_deans_post_class_schedules ORDER BY school_year");
$school_years_query->execute();
$school_years = $school_years_query->fetchAll(PDO::FETCH_ASSOC);

$courses_query = $conn->prepare("SELECT DISTINCT course FROM tbl_deans_post_class_schedules ORDER BY course");
$courses_query->execute();
$courses = $courses_query->fetchAll(PDO::FETCH_ASSOC);

$semesters_query = $conn->prepare("SELECT DISTINCT semester FROM tbl_deans_post_class_schedules ORDER BY semester");
$semesters_query->execute();
$semesters = $semesters_query->fetchAll(PDO::FETCH_ASSOC);

$filter_department = isset($_GET['department']) ? $_GET['department'] : null;
$filter_year = isset($_GET['year']) ? $_GET['year'] : null;
$filter_school_year = isset($_GET['school_year']) ? $_GET['school_year'] : null;
$filter_course = isset($_GET['course']) ? $_GET['course'] : null;
$filter_semester = isset($_GET['semester']) ? $_GET['semester'] : null;

$query = "SELECT * FROM tbl_deans_post_class_schedules WHERE 1=1";
if ($filter_department) {
    $query .= " AND department = :department";
}
if ($filter_school_year) {
    $query .= " AND school_year = :school_year";
}
if ($filter_year) {
    $query .= " AND year = :year";
}
if ($filter_course) {
    $query .= " AND course = :course";
}
if ($filter_semester) {
    $query .= " AND semester = :semester";
}

$query .= " ORDER BY year, course LIMIT :limit OFFSET :offset";
$schedules_query = $conn->prepare($query);
$schedules_query->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
$schedules_query->bindParam(':offset', $offset, PDO::PARAM_INT);

if ($filter_department) {
    $schedules_query->bindParam(':department', $filter_department);
}
if ($filter_school_year) {
    $schedules_query->bindParam(':school_year', $filter_school_year);
}
if ($filter_year) {
    $schedules_query->bindParam(':year', $filter_year);
}
if ($filter_course) {
    $schedules_query->bindParam(':course', $filter_course);
}
if ($filter_semester) {
    $schedules_query->bindParam(':semester', $filter_semester);
}

$schedules_query->execute();
$schedules = $schedules_query->fetchAll(PDO::FETCH_ASSOC);

$total_query = "SELECT COUNT(*) FROM tbl_deans_post_class_schedules WHERE 1=1";
if ($filter_department) {
    $total_query .= " AND department = :department";
}
if ($filter_school_year) {
    $total_query .= " AND school_year = :school_year";
}
if ($filter_year) {
    $total_query .= " AND year = :year";
}
if ($filter_course) {
    $total_query .= " AND course = :course";
}
if ($filter_semester) {
    $total_query .= " AND semester = :semester";
}

$total_query = $conn->prepare($total_query);

if ($filter_department) {
    $total_query->bindParam(':department', $filter_department);
}
if ($filter_school_year) {
    $total_query->bindParam(':school_year', $filter_school_year);
}
if ($filter_year) {
    $total_query->bindParam(':year', $filter_year);
}
if ($filter_course) {
    $total_query->bindParam(':course', $filter_course);
}
if ($filter_semester) {
    $total_query->bindParam(':semester', $filter_semester);
}

$total_query->execute();
$total_schedules = $total_query->fetchColumn();
$total_pages = ceil($total_schedules / $items_per_page);
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
                        <a class="dropdown-link active" href="class_schedules.php" id="">Class Schedules</a>
                        <a class="dropdown-link" href="enrollment_schedules.php" id="">Enrollment Schedules</a>
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
                <h1 class="font-weight-bold" style="color: white;">Class Schedules</h1>
                <p>Find your class schedules for the semester.</p>
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

                    <!-- Department filter -->
                    <div class="form-group col-md-3">
                        <label for="departmentFilter">Filter by Department:</label>
                        <select id="departmentFilter" name="department" class="form-control">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= htmlspecialchars($dept['department']) ?>" <?= $filter_department == $dept['department'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['department']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Year filter -->
                    <div class="form-group col-md-3">
                        <label for="yearFilter">Filter by Year:</label>
                        <select id="yearFilter" name="year" class="form-control">
                            <option value="">All Years</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= htmlspecialchars($year['year']) ?>" <?= $filter_year == $year['year'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year['year']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Course filter -->
                    <div class="form-group col-md-3">
                        <label for="courseFilter">Filter by Course:</label>
                        <select id="courseFilter" name="course" class="form-control">
                            <option value="">All Courses</option>
                        </select>
                    </div>

                    <!-- Semester filter -->
                    <div class="form-group col-md-3">
                        <label for="semesterFilter">Filter by Semester:</label>
                        <select id="semesterFilter" name="semester" class="form-control">
                            <option value="">All Semesters</option>
                            <?php foreach ($semesters as $semester) : ?>
                                <option value="<?= htmlspecialchars($semester['semester']) ?>" <?= $filter_semester == $semester['semester'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($semester['semester']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php if ($schedules) : ?>
                    <div class="d-flex justify-content-end" style="gap: 5px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="class_schedules.php" class="btn btn-secondary">Reset</a>
                    </div>
                <?php else : ?>
                <?php endif; ?>
            </form>
        </div>

        <div class="row">
            <?php if (count($schedules) > 0) : ?>
                <?php foreach ($schedules as $schedule) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card" style="border: 2px solid black; height: 300px">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($schedule['course']) ?></h5>
                                <p class="card-text" style="text-align: left !important;">
                                    <strong>PDF:</strong> <?= htmlspecialchars($schedule['schedule_upload']) ?><br>
                                    <strong>Year:</strong> <?= htmlspecialchars($schedule['year']) ?> / <?= htmlspecialchars($schedule['section']) ?><br>
                                    <strong>Department:</strong> <?= htmlspecialchars($schedule['department']) ?><br>
                                    <strong>Semester:</strong> <?= htmlspecialchars($schedule['semester']) ?>
                                </p>
                                <a href="assets/uploads/class_schedules/<?= htmlspecialchars($schedule['schedule_upload']) ?>" class="btn btn-primary bg-blue" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> View Schedule</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12" style="background-color: #001968; padding: 20px; margin-bottom: 10px;">
                    <p class="text-center" style="color: white; font-size: 20px !important;">No schedules available for the selected filters.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="pagination-container">
            <ul class="pagination">
                <?php if ($page > 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&department=<?= $filter_department ?>&year=<?= $filter_year ?>&course=<?= $filter_course ?>&semester=<?= $filter_semester ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&department=<?= $filter_department ?>&year=<?= $filter_year ?>&course=<?= $filter_course ?>&semester=<?= $filter_semester ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&department=<?= $filter_department ?>&year=<?= $filter_year ?>&course=<?= $filter_course ?>&semester=<?= $filter_semester ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
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
        const coursesByDepartment = {
            "COLLEGE OF SCIENCE AND TECHNOLOGY": [
                "BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY",
                "BACHELOR OF SCIENCE IN COMPUTER SCIENCE",
                "BACHELOR OF SCIENCE IN INFORMATION SYSTEM",
                "BACHELOR OF SCIENCE IN FOOD TECHNOLOGY"
            ],
            "COLLEGE OF BUSINESS MANAGEMENT": [
                "BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MARKETING MANAGEMENT",
                "BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT",
                "BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN HUMAN RESOURCES MANAGEMENT",
                "BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT",
                "BACHELOR OF SCIENCE IN ENTREPRENEURSHIP",
                "BACHELOR OF SCIENCE IN REAL ESTATE MANAGEMENT",
                "BACHELOR OF SCIENCE IN TOURISM MANAGEMENT"
            ],
            "COLLEGE OF CRIMINAL JUSTICE EDUCATION": [
                "BACHELOR OF SCIENCE IN CRIMINOLOGY"
            ],
            "COLLEGE OF ARTS AND SCIENCE": [
                "BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES",
                "BACHELOR OF PUBLIC ADMINISTRATION"
            ],
            "COLLEGE OF TEACHER EDUCATION": [
                "BACHELOR OF ELEMENTARY EDUCATION",
                "BACHELOR OF SECONDARY EDUCATION MAJOR IN ENGLISH",
                "BACHELOR OF SECONDARY EDUCATION MAJOR IN MATHEMATICS",
                "BACHELOR OF SECONDARY EDUCATION MAJOR IN FILIPINO",
                "BACHELOR OF SECONDARY EDUCATION MAJOR IN SOCIAL STUDIES",
                "BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN INDUSTRIAL ARTS",
                "BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN HOME ECONOMICS AND LIVELIHOOD EDUCATION"
            ],
            "COLLEGE OF AGRICULTURE SCIENCES": [
                "BACHELOR OF SCIENCE IN FISHERIES",
                "BACHELOR OF SCIENCE IN AGRICULTURE"
            ],
            "COLLEGE OF INDUSTRIAL ENGINEERING": [
                "BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY",
                "BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN ELECTRONICS TECHNOLOGY",
                "BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN MECHANICAL TECHNOLOGY",
                "BACHELOR OF SCIENCE IN ELECTRICAL ENGINEERING",
                "BACHELOR OF SCIENCE IN MECHANICAL ENGINEERING"
            ]
        };

        document.addEventListener('DOMContentLoaded', function() {
            const departmentDropdown = document.getElementById('departmentFilter');
            const courseDropdown = document.getElementById('courseFilter');
            const selectedDepartment = departmentDropdown.value;
            const filterCourse = new URLSearchParams(window.location.search).get('course'); // Get 'course' parameter from URL

            departmentDropdown.addEventListener('change', function() {
                const department = this.value;
                updateCourseOptions(department);
            });

            if (selectedDepartment) {
                updateCourseOptions(selectedDepartment);

                if (filterCourse) {
                    const option = document.querySelector(`#courseFilter option[value="${filterCourse}"]`);
                    if (option) {
                        option.selected = true;
                    }
                }
            }

            function updateCourseOptions(department) {
                courseDropdown.innerHTML = '<option value="">Select Course</option>';

                if (coursesByDepartment[department]) {
                    coursesByDepartment[department].forEach(function(course) {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        courseDropdown.appendChild(option);
                    });
                }
            }
        });
    </script>

</body>

</html>