<?php
include_once('include/common.php');

// $msg = '';
if (isset($_SESSION['login'])) {
  header("Location: index.php");
}


if (isset($_POST['login'])) {

  $sanitized_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  // If the data is valid
  if ($sanitized_data !== null) {
    // Process the sanitized data

    if (empty($_POST['login']) || empty($_POST['password'])) {

      setMessage("error", "Enter the correct email and password");
    } else {

      $before30Min = date("Y-m-d H:i:s", strtotime('-30 minutes', time()));

      $ip_address = $_SERVER['REMOTE_ADDR'];

      $canLogin = sqliteQuery("SELECT COUNT(*) AS invalid_login FROM invalid_login WHERE email = ? AND ip_address = ? AND attempted >= ? ", [$_POST['login'], $ip_address, $before30Min]);


      if (GetData($canLogin)['invalid_login'] > 5) {

        setMessage("error", "Your account has been locked for 30 minutes. Please try again after 30 minutes.");
      } else {

        $user = sqliteQuery("SELECT * FROM user WHERE email = ? AND disable = 0", [$_POST['login']])->fetchArray(SQLITE3_ASSOC);

        $remember_me = isset($_POST['remember_me']) ? 1 : 0;

        if (!empty($user) && password_verify($_POST['password'], $user['password']) == $user['password']) {

          $ip_address = $_SERVER['REMOTE_ADDR'];

          $getInvalidLogin = sqliteQuery("SELECT * FROM invalid_login  WHERE email = ? AND ip_address = ? ", [$_POST['login'], $ip_address]);

          if (!empty(GetData($getInvalidLogin))) {

            sqliteQuery("DELETE FROM invalid_login  WHERE email = ?  AND ip_address = ? ", [$_POST['login'], $ip_address]);
          }

          $_SESSION['login'] = $user['email'];
          $_SESSION['fullname'] = $user['name'];
          $_SESSION['group'] = $user['group_name'];
          $_SESSION['user_mail'] = $user['email'];
          $_SESSION['user_phone'] = $user['phone'];
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['avatar'] = $user['avatar'];

          header("Location: index.php");
        } else {

          $timestamp = date('Y-m-d G:i:s');

          $params = [$_SERVER['REMOTE_ADDR'], $_POST['login'], $timestamp];

          $query = "INSERT INTO invalid_login (ip_address, email, attempted) VALUES (?,?,?)";

          $insert = sqliteQuery($query, $params);

          setMessage("error", "Invalid login details");
        }

        // ---------------
      }

      // ---------------

    }
  }
}


if (isset($_POST['logout'])) {
  session_unset();
  session_destroy();
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Login | <?php echo SITE_NAME; ?></title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="template/bootstrap/css/bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="template/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="template/plugins/iCheck/square/blue.css">
  <!-- iCheck -->

</head>

<body class="hold-transition login-page <?= LANGUAGE; ?>">
  <div class="login-box">
    <div class="login-logo">
      <a style="font-size: 26px; font-weight: 400; color: #333;" href="/"><?= SITE_NAME ?></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">

      <?php getMessage(); ?>

      <form action="" method="post">

        <p class="login-box-msg">Sign in to start your session</p>
        <div class="form-group has-feedback">
          <input type="text" name="login" class="form-control" placeholder="Email ">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control" placeholder="Password ">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck" style="margin-right:5px; float:left;">
              <label>
                <input type="checkbox"> <span style="vertical-align:middle; margin-left:2px;">Remember me</span>
              </label>
            </div>
          </div><!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
          </div><!-- /.col -->
        </div>
      </form>
    </div><!-- /.login-box-body -->
  </div><!-- /.login-box -->
  <?php include('include/scripts.php'); ?>
  <script src="template/plugins/iCheck/icheck.min.js"></script>
  <script>
    $(function() {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>
</body>


</html>