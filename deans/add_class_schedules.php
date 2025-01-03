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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_year = $_POST['school_year'];
    $semester = $_POST['semester'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $course = $_POST['course'];
    $deans_id = $_POST['deans_id'];

    $schedule_upload = $_FILES['schedule_upload'];
    $file_name = basename($schedule_upload['name']);
    $target_dir = "../assets/uploads/class_schedules/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $file_name;

    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = array("pdf", "jpg", "jpeg", "png", "gif");
    if (!in_array($file_type, $allowed_types)) {
        echo "Only PDF, JPG, JPEG, PNG, or GIF files are allowed.";
        exit();
    }

    if (move_uploaded_file($schedule_upload['tmp_name'], $target_file)) {
        $query = "INSERT INTO tbl_deans_post_class_schedules (deans_id, school_year, semester, department, year, section, course, schedule_upload, created_at, updated_at) 
                  VALUES ('$deans_id', '$school_year', '$semester', '$department', '$year', '$section', '$course', '$file_name', NOW(), NOW())";

        if ($conn->query($query)) {
            $_SESSION['success'] = 'Class schedule added successfully!';
            header('Location: add_class_schedules.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Error uploading the schedule.';
        header('Location: add_class_schedules.php');
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
                            <a href="manage_deans.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Manage Deans
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="class_schedules.php" class="nav-link active">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Class Schedules
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="enrollment_schedules.php" class="nav-link">
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
                                <li class="breadcrumb-item"><a href="class_schedules.php">CLASS SCHEDULES</a></li>
                                <li class="breadcrumb-item active">ADD NEW CLASS SCHEDULES</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">ADD NEW CLASS SCHEDULES</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="" id="quickForm" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <input type="hidden" name="deans_id" class="form-control" value="<?php echo htmlspecialchars($admin_id); ?>">

                                        <div class="form-group">
                                            <label>School Year</label>
                                            <select class="form-control" id="school_year" name="school_year">
                                                <option value="">Select School Year</option>
                                                <option value="2023-2024">2023-2024</option>
                                                <option value="2024-2025">2024-2025</option>
                                                <option value="2025-2026">2025-2026</option>
                                                <option value="2026-2027">2026-2027</option>
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <label>Semester</label>
                                            <select class="form-control" id="semester" name="semester">
                                                <option value="">Select Semester</option>
                                                <option value="1st semester">1st semester</option>
                                                <option value="2nd semester">2nd semester</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Department</label>
                                            <select class="form-control" id="department" name="department">
                                                <option value="">Select Department</option>
                                                <option value="COLLEGE OF SCIENCE AND TECHNOLOGY">COLLEGE OF SCIENCE AND TECHNOLOGY</option>
                                                <option value="COLLEGE OF BUSINESS MANAGEMENT">COLLEGE OF BUSINESS MANAGEMENT</option>
                                                <option value="COLLEGE OF CRIMINAL JUSTICE EDUCATION">COLLEGE OF CRIMINAL JUSTICE EDUCATION</option>
                                                <option value="COLLEGE OF ARTS AND SCIENCE">COLLEGE OF ARTS AND SCIENCE</option>
                                                <option value="COLLEGE OF TEACHER EDUCATION">COLLEGE OF TEACHER EDUCATION</option>
                                                <option value="COLLEGE OF AGRICULTURE SCIENCES">COLLEGE OF AGRICULTURE SCIENCES</option>
                                                <option value="COLLEGE OF INDUSTRIAL ENGINEERING">COLLEGE OF INDUSTRIAL ENGINEERING</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Course</label>
                                            <select class="form-control" id="course" name="course">
                                                <option value="">Select Course</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Year Level</label>
                                            <select class="form-control" id="year" name="year">
                                                <option value="">Select Year Level</option>
                                                <option value="I">I</option>
                                                <option value="II">II</option>
                                                <option value="III">III</option>
                                                <option value="IV">IV</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Section</label>
                                            <select class="form-control" id="section" name="section">
                                                <option value="">Select Section</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                                <option value="E">E</option>
                                                <option value="F">F</option>
                                                <option value="G">G</option>
                                                <option value="H">H</option>
                                                <option value="I">I</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="scheduleUpload">Schedule PDF:</label>
                                            <input type="file" name="schedule_upload" class="form-control" id="scheduleUpload">
                                        </div>
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
                    school_year: {
                        required: true
                    },
                    semester: {
                        required: true
                    },
                    department: {
                        required: true
                    },
                    year: {
                        required: true
                    },
                    section: {
                        required: true
                    },
                    course: {
                        required: true
                    },
                    schedule_upload: {
                        required: true,
                    }
                },
                messages: {
                    school_year: {
                        required: "Please select a school year"
                    },
                    semester: {
                        required: "Please select a semester"
                    },
                    department: {
                        required: "Please select a department"
                    },
                    year: {
                        required: "Please select a year level"
                    },
                    section: {
                        required: "Please select a section"
                    },
                    course: {
                        required: "Please select a course"
                    },
                    schedule_upload: {
                        required: "Please upload a schedule PDF",
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

    <!-- MAPPING COURSE PER DEPARTMENT -->
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

        document.getElementById('department').addEventListener('change', function() {
            const department = this.value;
            const courseDropdown = document.getElementById('course');

            courseDropdown.innerHTML = '<option value="">Select Course</option>';

            if (coursesByDepartment[department]) {
                coursesByDepartment[department].forEach(function(course) {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseDropdown.appendChild(option);
                });
            }
        });
    </script>


</body>

</html>