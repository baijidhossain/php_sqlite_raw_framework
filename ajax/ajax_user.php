<?php
include_once('../include/common.php');
if ($_SESSION['group'] != "Admin") {
  die("You don't have permission to access this page.");
}

$mode = $_GET['mode'];


if ($mode == 'create') { ?>

  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        ×
      </button>
      <h4 class="modal-title">Add New User</h4>
    </div>
    <form action="" method="post" autocomplete="off">
      <div class="modal-body row">
        <div class="col-md-10 col-md-offset-1">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" maxlength="50" required>
          </div>
          <div class="form-group">
            <label>Email:</label>
            <input type="email" class="form-control" name="email" maxlength="">
          </div>
          <div class="form-group">
            <label>Phone:</label>
            <input type="number" class="form-control" name="phone" maxlength="15" required>
          </div>
          <div class="form-group">
            <label>Status:</label>
            <select class="form-control" name="status" required>
              <option value="active" selected="">Active</option>
              <option value="disable">Disable</option>
            </select>
          </div>
          <div class="form-group">
            <label>Group:</label>
            <select class="form-control" name="group" required>
              <option value="1">Admin</option>

            </select>
          </div>

          <div class="form-group">
            <label>Password:</label>

            <input id="password" type="password" pattern=".{8,}" class="form-control" name="password" title="8 characters minimum" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="addUser" class="btn btn-primary">Save
        </button>
      </div>

    </form>
  </div>

<?php } elseif ($mode == "edit") {

  $db = new SQLite3('../database.db');
  $id = $_GET['id'];
  $user = sqliteQuery("SELECT * FROM user WHERE id = ? ", [$id])->fetchArray(SQLITE3_ASSOC);
?>

  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        ×
      </button>
      <h4 class="modal-title">Update User</h4>
    </div>
    <form action="" method="post">
      <div class="modal-body row">
        <div class="col-md-10 col-md-offset-1">

          <div class="form-group">

            <input type="text" hidden name="uid" value="<?= $user['id'] ?>">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="<?= $user['name'] ?>" maxlength="50" required>

          </div>

          <div class="form-group">
            <label>Email:</label>
            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" maxlength="50" required>
          </div>
          <div class="form-group">
            <label>Phone:</label>
            <input type="number" class="form-control" name="phone" value="<?= $user['phone'] ?>" maxlength="15" required>
          </div>
          <div class="form-group">
            <label>Status:</label>
            <select class="form-control" name="status" required>
              <option <?= $user['disable'] == '0'  ? "selected" : "" ?> value="active" selected="">Active</option>
              <option <?= $user['disable'] == '1'  ? "selected" : "" ?> value="disable">Disabled</option>
            </select>
          </div>
          <div class="form-group">
            <label>Group:</label>
            <select class="form-control" name="group" required>
              <option <?= $user['group_id'] == '1'  ? "selected" : "" ?> value="1">Admin</option>
            </select>
          </div>

          <div class="form-group">
            <label>Password:</label>

            <input id="password" type="password" pattern=".{8,}" class="form-control" name="password" title="8 characters minimum">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="updateUser" class="btn btn-primary">Update
        </button>
      </div>

    </form>
  </div>

<?php
}






?>