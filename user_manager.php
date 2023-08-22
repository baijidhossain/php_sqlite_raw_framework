<?php
$pageTitle = "User manager";

include_once(realpath(dirname(__FILE__)) . '/include/common.php');

$g_name = [
  "1" => "Admin",
  "2" => "User"
];


if ($_SESSION['group'] != "Admin") {
  die("You don't have permission to access this page.");
}

if (isset($_POST['addUser']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

  if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['status'])) {

    setMessage("error", "Fill all the required field");
  } else {

    if (strlen($_POST['password']) > 8 && strlen($_POST['password']) < 20) {

      $params = [$_POST['email'], $_POST['phone']];

      $exists = sqliteQuery("SELECT * FROM user WHERE email= ?", [$_POST['email']]);

      if (!empty(GetData($exists))) {
        setMessage("error", "Email already exist");
      } else {

        $name =  $_POST['name'];

        $phone =  $_POST['phone'];

        $email =  $_POST['email'];

        $password =  password_hash($_POST['password'], PASSWORD_DEFAULT);

        $group_id =  $_POST['group'];

        $group_name = $g_name[$group_id];

        $status = ($_POST['status'] == "active") ? 0 : 1;

        $timestamp = date('Y-m-d G:i:s');

        $sqlite = "INSERT INTO user (name, phone, email,avatar, password, group_name, group_id, disable, created) VALUES (?,?,?,?,?,?,?,?,?)";

        $params = [$name, $phone, $email, "template/user/1.png", $password,  $group_name, $group_id, $status, $timestamp];

        $insert = sqliteQuery($sqlite, $params);

        if ($insert) {
          setMessage("success", "User successfully inserted");
        } else {
          setMessage("error", "Something went wrong");
        }
      }
    } else {
      setMessage("error", "Password must be between 8 and 20 characters");
    }
  }
}

if (isset($_POST['updateUser']) && $_SERVER['REQUEST_METHOD'] === 'POST') {


  if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['email'])  || empty($_POST['status']) || empty($_POST['uid'])) {

    setMessage("error", "Fill all the required field");
  } else {

    $uid = $_POST['uid'];

    $exists = sqliteQuery("SELECT * FROM user WHERE email= ? AND id !=?", [$_POST['email'], $uid]);

    if (!empty(GetData($exists))) {
      setMessage("error", "Email already in use");
    } else {
      if (strlen($_POST['name']) > 4) {

        $name =  $_POST['name'];

        $phone =  $_POST['phone'];

        $email =  $_POST['email'];

        $password =  password_hash($_POST['password'], PASSWORD_DEFAULT);

        $group_id =  $_POST['group'];

        $group_name = $g_name[$group_id];

        $status = ($_POST['status'] == "active") ? 0 : 1;

        $timestamp = date('Y-m-d G:i:s');

        $insert = sqliteQuery("UPDATE  user SET name = ?, phone =?, email= ?, group_name = ?, group_id =?, disable = ? WHERE id = ?", [$name, $phone, $email, $group_name, $group_id, $status, $uid]);

        if ($insert) {
          setMessage("success", "User successfully updated");
        } else {

          setMessage("error", "Something went wrong");
        }
      } else {
        setMessage("error", "ou have not filled the form correctly");
      }
    }
  }

  if (!empty($_POST['password']) || empty($_POST['uid'])) {

    $password = $_POST['password'];

    if (strlen($password) < 8 || strlen($password) > 20) {
      setMessage("error", "Password must be between 8 and 20 characters");
    } else {

      $uid = $_POST['uid'];

      $password  = password_hash($password, PASSWORD_DEFAULT);

      $update = sqliteQuery("UPDATE user SET password=? WHERE id=?", [$password, $uid]);

      if ($update) {

        setMessage("success", "User and password successfully updated");
      } else {
        setMessage("error", "Password update failed");
      }
    }
  }
  // Password update

}

if (isset($_GET['status']) && isset($_GET['uid'])) {

  if (is_numeric($_GET['status']) && is_numeric($_GET['uid'])) {

    $updated  = sqliteQuery("UPDATE  user SET disable = ? WHERE id = ?", [$_GET['status'], $_GET['uid']]);
  }
}
require_once(realpath(dirname(__FILE__)) . '/include/template_header.php');

$users = SQLiteDataPagination("SELECT * FROM user");

?>

<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Users
    </h1>
    <ol class="breadcrumb">
      <li><a href="/"><i class="fas fa-tachometer-alt mr-1"></i>Home</a></li>
      <li class="active">Users</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php getMessage() ?>

    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">User List</h3>
            <div class="box-tools pull-right">
              <a data-toggle="modal" data-target="#myModal" href="ajax/ajax_user.php?mode=create" class="box-top btn btn-sm btn-primary py-0004">
                <i class="fa fa-user mr-1"></i>
                Add User
              </a>

            </div>
            <!-- /.box-tools -->
          </div>
          <div class="box-body">


            <div class="table-responsive">
              <table class="table table-bordered ws_nowrap table-hover mb-0">
                <thead>
                  <tr>
                    <th style="width: 270px;">Name</th>
                    <th style="width: 320px;">Email</th>
                    <th>Phone</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th>DateTime</th>
                    <th>Manage</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  if (empty($users['data'])) { ?>
                    <tr>
                      <td colspan="10" class="text-center">No Data Found</td>
                    </tr>
                    <?php } else {
                    foreach ($users['data'] as $user) { ?>

                      <tr>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['phone'] ?></td>
                        <td>
                          <span class="badge bg-aqua font-light"><?= $user['group_name'] ?></span>
                        </td>
                        <td>
                          <?php

                          if ($user['disable'] == 1) {
                            echo '<i class="fa fa-times-circle text-danger"></i>';
                          } else {
                            echo ' <i class="fa fa-check-circle text-success"></i>';
                          }

                          ?>

                        </td>
                        <td><?= date_create($user['created'])->format('d M, Y H:s A') ?></td>
                        <td>
                          <a data-toggle="modal" data-target="#myModal" href="ajax/ajax_user.php?mode=edit&id=<?= $user['id'] ?>" class="edit text-info" style="margin-right:10px"><i class="fa fa-edit"></i> Modify</a>

                          <?php

                          if ($user['disable'] == "0") { ?>
                            <a href="javascript:ChangeStatus(<?= $user['id'] ?>,1)" style="margin-right:10px">
                              <span class="text-danger"><i class="fa fa-times-circle"></i> Deactivate</span> </a>

                          <?php } else { ?>

                            <a href="javascript:ChangeStatus(<?= $user['id'] ?>,0)" style="margin-right:10px">
                              <span class="text-success"><i class="fa fa-check-circle"></i> Active</span> </a>
                          <?php  }

                          ?>

                        </td>
                      </tr>

                  <?php }
                  }
                  ?>

                </tbody>
              </table>
            </div>
          </div>
          <div class="box-footer">
            <?= $users['info'] ?>
            <?= $users['pagination'] ?>
          </div>
        </div>
      </div>
    </div>

  </section>


</div>
<!-- /.content-wrapper -->


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<?php include('include/footer.php'); ?>

</div><!-- ./wrapper -->

<?php include('include/scripts.php'); ?>

<script>
  $(document).ready(function() {
    $('#cpass').click(function() {
      if ($("#newpass").val() != $("#newpass2").val()) {
        alert("New and confirm passwords is incorrect");
        return false;
      } else {
        return true;
      }
    });

  });


  $(document).on('hidden.bs.modal', function(e) {
    $(e.target).removeData('bs.modal');
  });

  $(document).ready(function() {
    $('.del').click(function() {
      var cnf = confirm('Are you sure want to delete this user?');
      if (cnf) {
        $('#del_id').val($(this).attr("data-id"));
        $('#del_form').submit();
      }
    });

  });


  $('#myModal').on('hidden.bs.modal', function() {
    $('.modal-content').html('');
  });

  function ChangeStatus(id, status) {

    if (status === 1 || status === 0) {
      let method = status === 0 ? 'activate' : 'deactivate';
      let conf = confirm(`Are you sure want to ${method} this user?`);
      if (conf) {
        location.replace("user_manager.php?status=" + status + "&uid=" +
          id) // XOR method to automatically change 0 or 1
        window.location = window.location.pathname
      }
    }

  }

  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

</body>

</html>