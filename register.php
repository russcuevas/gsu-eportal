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
  <!-- Icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <style>
    .register-box {
      width: 60% !important;
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
        <form method="post">
          <label style="text-align: center !important;" for="">STATUS</label>
          <div class="input-group mb-3">
            <select class="form-control" name="" id="">
              <option value="graduate">Graduate</option>
              <option value="old">Old</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="">Student ID</label>
              <div class="input-group mb-3">
                <input type="text" class="form-control">
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
                <select class="form-control" name="" id="">
                  <option value="I">I - College</option>
                  <option value="II">II - College</option>
                  <option value="III">III - College</option>
                  <option value="IV">IV - College</option>
                </select>
              </div>
              <label for="">Course</label>
              <div class="input-group mb-3">
                <select class="form-control" name="" id="">
                  <option value="BSIT">Bachelor of Science in Information Technology</option>
                  <option value="II">II - College</option>
                  <option value="III">III - College</option>
                  <option value="IV">IV - College</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="">Fullname</label>
              <div class="input-group mb-3">
                <input type="text" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
              <label for="">Age</label>
              <div class="input-group mb-3">
                <input type="text" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
              <label for="">Gender</label>
              <div class="input-group mb-3">
                <input type="radio" name="fav_language" value="male">
                <label style="margin-top: 5px;">&nbsp; Male</label> &nbsp;&nbsp;&nbsp;
                <input type="radio" name="fav_language" value="female">
                <label style="margin-top: 5px;">&nbsp; Female</label>
              </div>
            </div>

            <div class="col-md-6">
              <label for="">Email</label>
              <div class="input-group mb-3">
                <input type="email" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                  </div>
                </div>
              </div>

              <label for="">Password</label>
              <div class="input-group mb-3">
                <input type="password" class="form-control">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                  </div>
                </div>
              </div>

              <label for="">Confirm Password</label>
              <div class="input-group mb-3">
                <input type="password" class="form-control">
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
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
</body>

</html>