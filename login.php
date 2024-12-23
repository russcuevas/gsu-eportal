<?php
session_start();
include('database/connection.php');

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = sha1($_POST['password']);

  $select_users = $conn->prepare("SELECT * FROM `tbl_users` WHERE email = ? AND password = ?");
  $select_users->execute([$email, $password]);
  if ($select_users->rowCount() > 0) {
    $user = $select_users->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['student_id'] = $user['student_id'];
    $_SESSION['age'] = $user['age'];
    $_SESSION['year'] = $user['year'];
    $_SESSION['course'] = $user['course'];
    $_SESSION['gender'] = $user['gender'];
    $_SESSION['status'] = $user['status'];
    header('location:index.php');
  } else {
    $_SESSION['error_message'] = 'Incorrect email or password';
    header('location:login.php');
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
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href=""><img style="width: 80px; height: 80px;" src="assets/images/gsu-logo.jpg" alt=""></a><br>
        <a class="h1"><b>GSU</b>| e-Request</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="" method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- /.col -->
            <div class="col-12">
              <button type="submit" name="login" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <p class="mb-0">
          <a href="register.php" class="text-center">Register here</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
  <!-- SweetAlert JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>

  <?php
  if (isset($_SESSION['error_message'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '" . $_SESSION['error_message'] . "',
        });
    </script>";
    unset($_SESSION['error_message']);
  }
  ?>
</body>

</html>