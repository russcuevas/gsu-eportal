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

if (isset($_GET['id'])) {
    $schedule_id = $_GET['id'];
    $query = "SELECT * FROM `tbl_deans_post_class_schedules` WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
    $class_schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class_schedule) {
        echo "Class schedule not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_year = $_POST['school_year'];
    $semester = $_POST['semester'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $course = $_POST['course'];

    $schedule_upload = $class_schedule['schedule_upload'];
    if (isset($_FILES['schedule_upload']) && $_FILES['schedule_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/class_schedules/';
        $new_file_name = time() . '_' . basename($_FILES['schedule_upload']['name']);
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['schedule_upload']['tmp_name'], $upload_path)) {
            $schedule_upload = $new_file_name;
        } else {
            $_SESSION['error'] = 'Failed to upload file.';
            header('Location: edit_class_schedules.php');
            exit();
        }
    }

    $update_query = "UPDATE `tbl_deans_post_class_schedules` SET school_year = :school_year, semester = :semester, department = :department, year = :year, section = :section, course = :course, schedule_upload = :schedule_upload, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':school_year', $school_year);
    $stmt->bindParam(':semester', $semester);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':section', $section);
    $stmt->bindParam(':course', $course);
    $stmt->bindParam(':schedule_upload', $schedule_upload);
    $stmt->bindParam(':id', $schedule_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Class schedule updated successfully!';
        header('Location: class_schedules.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error updating schedule!';
        header('Location: class_schedules.php');
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
                                <li class="breadcrumb-item active">EDIT CLASS SCHEDULES</li>
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
                                    <h3 class="card-title" style="font-size: 25px;">EDIT CLASS SCHEDULES</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form action="" id="quickForm" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <!-- Hidden Field for Deans ID -->
                                        <input type="hidden" name="deans_id" class="form-control" value="<?php echo htmlspecialchars($admin_id); ?>">

                                        <!-- School Year -->
                                        <div class="form-group">
                                            <label>School Year</label>
                                            <select class="form-control" id="school_year" name="school_year" required>
                                                <option value="">Select School Year</option>
                                                <option value="2023-2024" <?php echo $class_schedule['school_year'] === '2023-2024' ? 'selected' : ''; ?>>2023-2024</option>
                                                <option value="2024-2025" <?php echo $class_schedule['school_year'] === '2024-2025' ? 'selected' : ''; ?>>2024-2025</option>
                                                <option value="2025-2026" <?php echo $class_schedule['school_year'] === '2025-2026' ? 'selected' : ''; ?>>2025-2026</option>
                                                <option value="2026-2027" <?php echo $class_schedule['school_year'] === '2026-2027' ? 'selected' : ''; ?>>2026-2027</option>
                                            </select>
                                        </div>

                                        <!-- Semester -->
                                        <div class="form-group">
                                            <label>Semester</label>
                                            <select class="form-control" id="semester" name="semester" required>
                                                <option value="">Select Semester</option>
                                                <option value="1st semester" <?php echo $class_schedule['semester'] === '1st semester' ? 'selected' : ''; ?>>1st Semester</option>
                                                <option value="2nd semester" <?php echo $class_schedule['semester'] === '2nd semester' ? 'selected' : ''; ?>>2nd Semester</option>
                                            </select>
                                        </div>

                                        <!-- Department -->
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select class="form-control" id="department" name="department">
                                                <option value="">Select Department</option>
                                                <option value="COLLEGE OF SCIENCE AND TECHNOLOGY"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF SCIENCE AND TECHNOLOGY' ? 'selected' : ''; ?>>
                                                    COLLEGE OF SCIENCE AND TECHNOLOGY
                                                </option>
                                                <option value="COLLEGE OF BUSINESS MANAGEMENT"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF BUSINESS MANAGEMENT' ? 'selected' : ''; ?>>
                                                    COLLEGE OF BUSINESS MANAGEMENT
                                                </option>
                                                <option value="COLLEGE OF CRIMINAL JUSTICE EDUCATION"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF CRIMINAL JUSTICE EDUCATION' ? 'selected' : ''; ?>>
                                                    COLLEGE OF CRIMINAL JUSTICE EDUCATION
                                                </option>
                                                <option value="COLLEGE OF ARTS AND SCIENCE"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF ARTS AND SCIENCE' ? 'selected' : ''; ?>>
                                                    COLLEGE OF ARTS AND SCIENCE
                                                </option>
                                                <option value="COLLEGE OF TEACHER EDUCATION"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF TEACHER EDUCATION' ? 'selected' : ''; ?>>
                                                    COLLEGE OF TEACHER EDUCATION
                                                </option>
                                                <option value="COLLEGE OF AGRICULTURE SCIENCES"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF AGRICULTURE SCIENCES' ? 'selected' : ''; ?>>
                                                    COLLEGE OF AGRICULTURE SCIENCES
                                                </option>
                                                <option value="COLLEGE OF INDUSTRIAL ENGINEERING"
                                                    <?php echo $class_schedule['department'] === 'COLLEGE OF INDUSTRIAL ENGINEERING' ? 'selected' : ''; ?>>
                                                    COLLEGE OF INDUSTRIAL ENGINEERING
                                                </option>
                                            </select>
                                        </div>


                                        <!-- Course -->
                                        <div class="form-group">
                                            <label>Course</label>
                                            <select class="form-control" id="course" name="course">
                                                <option value="">Select Course</option>
                                            </select>
                                        </div>

                                        <!-- Year Level -->
                                        <div class="form-group">
                                            <label>Year Level</label>
                                            <select class="form-control" id="year" name="year" required>
                                                <option value="">Select Year Level</option>
                                                <option value="I" <?php echo $class_schedule['year'] === 'I' ? 'selected' : ''; ?>>I</option>
                                                <option value="II" <?php echo $class_schedule['year'] === 'II' ? 'selected' : ''; ?>>II</option>
                                                <option value="III" <?php echo $class_schedule['year'] === 'III' ? 'selected' : ''; ?>>III</option>
                                                <option value="IV" <?php echo $class_schedule['year'] === 'IV' ? 'selected' : ''; ?>>IV</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Section</label>
                                            <select class="form-control" id="section" name="section">
                                                <option value="">Select Section</option>
                                                <option value="A" <?php echo $class_schedule['section'] === 'A' ? 'selected' : ''; ?>>A</option>
                                                <option value="B" <?php echo $class_schedule['section'] === 'B' ? 'selected' : ''; ?>>B</option>
                                                <option value="C" <?php echo $class_schedule['section'] === 'C' ? 'selected' : ''; ?>>C</option>
                                                <option value="D" <?php echo $class_schedule['section'] === 'D' ? 'selected' : ''; ?>>D</option>
                                                <option value="E" <?php echo $class_schedule['section'] === 'E' ? 'selected' : ''; ?>>E</option>
                                                <option value="F" <?php echo $class_schedule['section'] === 'F' ? 'selected' : ''; ?>>F</option>
                                                <option value="G" <?php echo $class_schedule['section'] === 'G' ? 'selected' : ''; ?>>G</option>
                                                <option value="H" <?php echo $class_schedule['section'] === 'H' ? 'selected' : ''; ?>>H</option>
                                                <option value="I" <?php echo $class_schedule['section'] === 'I' ? 'selected' : ''; ?>>I</option>
                                            </select>
                                        </div>

                                        <!-- Schedule File Upload -->
                                        <div class="form-group">
                                            <label for="schedule_upload">Upload Schedule (PDF/Image)</label>
                                            <input type="file" id="schedule_upload" name="schedule_upload" class="form-control">
                                            <small>Current File: <?php echo htmlspecialchars($class_schedule['schedule_upload']); ?></small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex" style="gap: 5px;">
                                        <button type="submit" class="btn btn-primary ml-auto">Update</button>
                                        <a href="class_schedules.php" class="btn btn-secondary">Cancel</a>
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
        $(function() {
            $('#quickForm').validate({
                rules: {
                    school_year: {
                        required: true
                    },
                    department: {
                        required: true
                    },
                    year: {
                        required: true
                    },
                    course: {
                        required: true
                    },
                },
                messages: {
                    school_year: {
                        required: "Please select a school year"
                    },
                    department: {
                        required: "Please select a department"
                    },
                    year: {
                        required: "Please select a year level"
                    },
                    course: {
                        required: "Please select a course"
                    },
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

    <!-- UPDATE COURSE MAPPING -->
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

        const departmentDropdown = document.getElementById('department');
        const courseDropdown = document.getElementById('course');

        departmentDropdown.addEventListener('change', function() {
            const department = this.value;

            courseDropdown.innerHTML = '<option value="">Select Course</option>';

            if (coursesByDepartment[department]) {
                coursesByDepartment[department].forEach(function(course) {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;

                    if (course === "<?php echo $class_schedule['course']; ?>") {
                        option.selected = true;
                    }

                    courseDropdown.appendChild(option);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            departmentDropdown.dispatchEvent(new Event('change'));
        });
    </script>
</body>

</html>