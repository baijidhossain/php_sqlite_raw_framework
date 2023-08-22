<?php
$pageTitle = "My Account";
include_once(realpath(dirname(__FILE__)) . '/include/common.php');

if (isset($_POST['changePassword']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

  if (empty($_POST['current_password']) || empty($_POST['newpass']) || empty($_POST['newpass2'])) {
    setMessage("error", "Fill all the required field");
  } else {

    if (strlen($_POST['newpass']) < 8 || strlen($_POST['newpass']) > 20) {

      setMessage("error", "Password must be between 8 and 20 characters");
    } else {

      $user = sqliteQuery("SELECT * FROM user WHERE id = ?", [$_SESSION['user_id']])->fetchArray(SQLITE3_ASSOC);

      if (password_verify($_POST['current_password'], $user['password']) && ($_POST['newpass'] == $_POST['newpass2'])) {

        $password_hash =  password_hash($_POST['newpass'], PASSWORD_DEFAULT);

        $change_pass =  sqliteQuery("UPDATE user SET password = ? WHERE id = ? ",  [$password_hash, $_SESSION['user_id']]);

        if ($change_pass) {

          setMessage("success", "Password successfully updated");
        } else {
          setMessage("error", "Something went wrong");
        }
      } else {
        setMessage("error", "Incorrect Password");
      }
    }
  }
}

if (isset($_POST['accountUpdate']) && $_SERVER['REQUEST_METHOD'] === 'POST') {


  if ($_FILES['avatar']['tmp_name'] != "") {
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check !== false) {
      if ($_FILES["avatar"]["size"] > 500000) {

        setMessage("error", "You can upload images of maximum 500 KB");
      } else {

        $ext = pathinfo(basename($_FILES["avatar"]["name"]));
        $ext = $ext['extension'];
        $save_path = 'template/user/' . $_SESSION['user_id'] . '.png';

        if (file_exists($save_path)) {
          unlink($save_path);
        }

        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $save_path)) {

          $updateQuery = "UPDATE user SET avatar=? WHERE id=? ";

          $params = [$save_path, $_SESSION['user_id']];

          $update =  sqliteQuery($updateQuery, $params);

          if ($update) {

            setMessage("success", "Profile image successfully updated");
          } else {
            setMessage("error", "Something went wrong");
          }
        } else {
          setMessage("error", "Image updated failed");
        }
      }
    } else {
      setMessage("error", "Invalid profile image");
    }
  }
  // Profile image update

  if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone'])) {
    setMessage("error", "Fill all the required field");
  } else {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    $updateQuery = "UPDATE user SET name=?, phone=?, email=? WHERE id=?";

    $params = [$name, $phone, $email, $user_id];

    $update = sqliteQuery($updateQuery, $params);

    if ($update) {
      setMessage("success", "Account successfully updated");
    } else {

      setMessage("error", "Something went wrong");
    }
  }
  // Account update

}


if (isset($_GET['mode'])) {
  $mode = $_GET['mode'];
} else {
  $mode = "";
}

require_once(realpath(dirname(__FILE__)) . '/include/template_header.php');

$userinfo =  sqliteQuery("SELECT * FROM user WHERE id = ? ", [$_SESSION['user_id']])->fetchArray(SQLITE3_ASSOC);

?>

<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      My Account
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fas fa-tachometer-alt mr-1"></i>Home</a></li>
      <li class="active">My Account</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php getMessage() ?>
    <div class="row">
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title bn">Profile</h3>

          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form action="" method="post" enctype="multipart/form-data">
            <div class="box-body">

              <div class="row">
                <div style="padding-top:10px;" class="col-md-4 text-center">
                  <?php
                  echo ($userinfo['avatar'] == "" ? '<img class="usr-info" src="template/user/no-profile.jpg">' : '<img class="usr-info" src="' . $userinfo['avatar'] . '">');
                  echo ($mode == 'edit' ? '<div class="photo-up">Upload Photo<input type="file" name="avatar"></div>' : '<p class="photo_text"> Photo</p>');
                  ?>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $userinfo['name']; ?>" maxlength="50" <?php echo ($mode == 'edit' ? '' : 'readonly'); ?> required>
                  </div>
                  <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo $userinfo['phone']; ?>" minlength="11" maxlength="15" <?php echo ($mode == 'edit' ? '' : 'readonly'); ?>>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $userinfo['email']; ?>" maxlength="" <?php echo ($mode == 'edit' ? '' : 'readonly'); ?>>

                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
              <?php
              if ($mode == 'edit') {

                echo '<input type="submit" class="btn btn-sm bn btn-primary pull-right" name="accountUpdate" value="Update">';
              } else {
                echo '<a href="?mode=edit" class="btn btn-sm bn btn-primary pull-right">Edit</a>';
              }
              ?>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Change Password</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form action="" method="post">
            <div class="box-body">
              <div class="form-group">
                <label>Current Password</label>
                <input type="password" class="form-control" name="current_password" required>
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control" name="newpass" required>
              </div>
              <div class="form-group">
                <label>Confirm Password</label>
                <input id="newpass2" type="password" class="form-control" name="newpass2" required>
              </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
              <input type="submit" name="changePassword" class="btn bn btn-sm btn-primary pull-right" value="Change">
            </div>
          </form>
        </div>
      </div>
    </div>

  </section>
</div><!-- /.content-wrapper -->

<?php include('include/footer.php'); ?>


</div><!-- ./wrapper -->


<?php include('include/scripts.php'); ?>

<script>
  $(document).ready(function() {
    $('#cpass').click(function() {
      if ($("#newpass").val() != $("#newpass2").val()) {
        alert("New and confirm password don't match");
        return false;
      } else {
        return true;
      }
    });

  });
</script>
</body>

</html>