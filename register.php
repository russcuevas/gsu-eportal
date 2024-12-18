<?php
session_start();
include('database/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $status = isset($_POST['status']) ? $_POST['status'] : '';
  $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';
  $year = isset($_POST['year']) ? $_POST['year'] : '';
  $course = isset($_POST['course']) ? $_POST['course'] : '';
  $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
  $age = isset($_POST['age']) ? $_POST['age'] : '';
  $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

  if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match!";
    header("Location: register.php");
    exit();
  }

  $email_check_query = "SELECT COUNT(*) FROM tbl_users WHERE email = :email";
  $stmt = $conn->prepare($email_check_query);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();

  $email_exists = $stmt->fetchColumn();

  if ($email_exists > 0) {
    $_SESSION['error'] = "The email address is already taken. Please use another email.";
    header("Location: register.php");
    exit();
  }

  $hashed_password = sha1($password);
  $sql = "INSERT INTO tbl_users (status, student_id, year, course, fullname, age, gender, email, password)
            VALUES (:status, :student_id, :year, :course, :fullname, :age, :gender, :email, :password)";

  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
  $stmt->bindParam(':year', $year, PDO::PARAM_STR);
  $stmt->bindParam(':course', $course, PDO::PARAM_STR);
  $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
  $stmt->bindParam(':age', $age, PDO::PARAM_INT);
  $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

  $stmt->execute();

  if ($stmt->rowCount() > 0) {
    $_SESSION['success'] = "Registration successful!";
    header("Location: register.php");
    exit();
  } else {
    $_SESSION['error'] = "Failed to register user.";
    header("Location: register.php");
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Sweetalert -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
  <!-- Icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <style>
    .register-box {
      width: 60% !important;
    }

    .invalid-feedback {
      display: block;
      color: red;
    }

    .is-invalid {
      border-color: red;
    }
  </style>
</head>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href=""><img style="width: 80px; height: 80px;" src="assets/images/gsu-logo.jpg" alt=""></a><br>
        <a class="h1"><b>GSU</b>| e-Request</a>
      </div>
      <div class="card-body">
        <form id="quickForm" method="post">
          <label style="text-align: center !important;" for="">STATUS</label>
          <div class="input-group mb-3">
            <select class="form-control" name="status" id="status">
              <option value="graduate">Graduate</option>
              <option value="old">Old</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="">Student ID</label>
              <div class="input-group mb-3">
                <input type="text" name="student_id" id="student_id" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label for="">Year</label>
              <div class="input-group mb-3">
                <select class="form-control" name="year" id="year">
                  <option value="1">I - College</option>
                  <option value="2">II - College</option>
                  <option value="3">III - College</option>
                  <option value="4">IV - College</option>
                </select>
              </div>
              <label for="">Course</label>
              <div class="input-group mb-3">
                <select class="form-control" name="course" id="course">
                  <option value="BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY">BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY</option>
                  <option value="BACHELOR OF SCIENCE IN COMPUTER SCIENCE">BACHELOR OF SCIENCE IN COMPUTER SCIENCE</option>
                  <option value="BACHELOR OF SCIENCE IN INFORMATION SYSTEM">BACHELOR OF SCIENCE IN INFORMATION SYSTEM</option>
                  <option value="BACHELOR OF SCIENCE IN FOOD TECHNOLOGY">BACHELOR OF SCIENCE IN FOOD TECHNOLOGY</option>
                  <option value="BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MARKETING MANAGEMENT">BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MARKETING MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT">BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN HUMAN RESOURCES MANAGEMENT">BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN HUMAN RESOURCES MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT">BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN ENTREPRENEURSHIP">BACHELOR OF SCIENCE IN ENTREPRENEURSHIP</option>
                  <option value="BACHELOR OF SCIENCE IN REAL ESTATE MANAGEMENT">BACHELOR OF SCIENCE IN REAL ESTATE MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN TOURISM MANAGEMENT">BACHELOR OF SCIENCE IN TOURISM MANAGEMENT</option>
                  <option value="BACHELOR OF SCIENCE IN CRIMINOLOGY">BACHELOR OF SCIENCE IN CRIMINOLOGY</option>
                  <option value="BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES">BACHELOR OF ARTS IN ENGLISH LANGUAGE STUDIES</option>
                  <option value="BACHELOR OF PUBLIC ADMINISTRATION">BACHELOR OF PUBLIC ADMINISTRATION</option>
                  <option value="BACHELOR OF ELEMENTARY EDUCATION">BACHELOR OF ELEMENTARY EDUCATION</option>
                  <option value="BACHELOR OF SECONDARY EDUCATION MAJOR IN ENGLISH">BACHELOR OF SECONDARY EDUCATION MAJOR IN ENGLISH</option>
                  <option value="BACHELOR OF SECONDARY EDUCATION MAJOR IN MATHEMATICS">BACHELOR OF SECONDARY EDUCATION MAJOR IN MATHEMATICS</option>
                  <option value="BACHELOR OF SECONDARY EDUCATION MAJOR IN FILIPINO">BACHELOR OF SECONDARY EDUCATION MAJOR IN FILIPINO</option>
                  <option value="BACHELOR OF SECONDARY EDUCATION MAJOR IN SOCIAL STUDIES">BACHELOR OF SECONDARY EDUCATION MAJOR IN SOCIAL STUDIES</option>
                  <option value="BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN INDUSTRIAL ARTS">BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN INDUSTRIAL ARTS</option>
                  <option value="BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN HOME ECONOMICS AND LIVELIHOOD EDUCATION">BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION MAJOR IN HOME ECONOMICS AND LIVELIHOOD EDUCATION</option>
                  <option value="BACHELOR OF SCIENCE IN FISHERIES">BACHELOR OF SCIENCE IN FISHERIES</option>
                  <option value="BACHELOR OF SCIENCE IN AGRICULTURE">BACHELOR OF SCIENCE IN AGRICULTURE</option>
                  <option value="BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY">BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY</option>
                  <option value="BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN ELECTRONICS TECHNOLOGY">BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN ELECTRONICS TECHNOLOGY</option>
                  <option value="BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN MECHANICAL TECHNOLOGY">BACHELOR OF INDUSTRIAL TECHNOLOGY MAJOR IN MECHANICAL TECHNOLOGY</option>
                  <option value="BACHELOR OF SCIENCE IN ELECTRICAL ENGINEERING">BACHELOR OF SCIENCE IN ELECTRICAL ENGINEERING</option>
                  <option value="BACHELOR OF SCIENCE IN MECHANICAL ENGINEERING">BACHELOR OF SCIENCE IN MECHANICAL ENGINEERING</option>
                </select>

              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="">Fullname</label>
              <div class="input-group mb-3">
                <input type="text" name="fullname" id="fullname" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
              <label for="">Age</label>
              <div class="input-group mb-3">
                <input type="text" name="age" id="age" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
              <label for="">Gender</label>
              <div class="input-group mb-3">
                <input type="radio" name="gender" value="male">
                <label style="margin-top: 5px;">&nbsp; Male</label> &nbsp;&nbsp;&nbsp;
                <input type="radio" name="gender" value="female">
                <label style="margin-top: 5px;">&nbsp; Female</label>
              </div>
            </div>

            <div class="col-md-6">
              <label for="">Email</label>
              <div class="input-group mb-3">
                <input type="email" name="email" id="email" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                  </div>
                </div>
              </div>

              <label for="">Password</label>
              <div class="input-group mb-3">
                <input type="password" name="password" id="password" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                  </div>
                </div>
              </div>

              <label for="">Confirm Password</label>
              <div class="input-group mb-3">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
          </div>
        </form>

        <a href="login.php" class="text-center">Login here</a>
      </div>
    </div>
  </div>

  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- jquery-validation -->
  <script src="assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="assets/plugins/jquery-validation/additional-methods.min.js"></script>
  <!-- sweetalert -->
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
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>

  <script>
    $(function() {
      $('#quickForm').validate({
        rules: {
          status: {
            required: true
          },
          student_id: {
            required: true,
            digits: true,
            minlength: 5,
            maxlength: 20
          },
          year: {
            required: true
          },
          course: {
            required: true
          },
          fullname: {
            required: true,
            minlength: 3
          },
          age: {
            required: true,
            digits: true,
            min: 18,
            max: 100
          },
          gender: {
            required: true
          },
          email: {
            required: true,
            email: true
          },
          password: {
            required: true,
            minlength: 6
          },
          confirm_password: {
            required: true,
            equalTo: "#password"
          }
        },
        messages: {
          status: {
            required: "Please select a status"
          },
          student_id: {
            required: "Please enter a student ID",
            digits: "Student ID must be a number",
          },
          year: {
            required: "Please select a year"
          },
          course: {
            required: "Please select a course"
          },
          fullname: {
            required: "Please enter your full name",
            minlength: "Full name must be at least 3 characters long"
          },
          age: {
            required: "Please enter your age",
            digits: "Age must be a number",
            min: "Age must be at least 18",
            max: "Age must be no more than 100"
          },
          gender: {
            required: "Please select your gender"
          },
          email: {
            required: "Please enter an email address",
            email: "Please enter a valid email address"
          },
          password: {
            required: "Please enter a password",
            minlength: "Password must be at least 6 characters long"
          },
          confirm_password: {
            required: "Please confirm your password",
            equalTo: "Password confirmation does not match"
          }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
          error.addClass('invalid-feedback');
          element.closest('.input-group').append(error);
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