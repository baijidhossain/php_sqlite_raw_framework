<?php
session_start();
// include config file and set language
include_once('config.php');


// run code only specified domain name defined in config.php
if ($_SERVER["SERVER_NAME"] != DOMAIN_NAME && DOMAIN_LOCK) {
  header("Location: http://" . DOMAIN_NAME); //if wrong server, then redirect
  die();
}

if (FORCE_HTTPS) {
  if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
  }
}

// if (SHOW_ERRORS) {
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// }

// if session login is not set and not logged from login.php file, then redirect to login page
if (!isset($_SESSION['login']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
  header('Location: login.php');
  die();
}

define('inSystem', true);
// default timezone
date_default_timezone_set("Asia/Dhaka");
// timestamp format
$timestamp = date("Y-m-d H:i:s");
// empty success or error message
$msg = '';
$db = '';
// get mini logo
function GetMLogo()
{
  echo '<span class="logo-mini">A</span>';
}

// get Logo
function GetLogo()
{
  echo 'ASSETS';
}

// get default profile image location
function GetAvatar()
{
  echo 'template/dist/img/no-profile.jpg';
}

// user profile panel
function GenerateUserBox()
{
  echo '<li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="' . $_SESSION['avatar'] . '" class="user-image" alt="User Image">
                  <span class="hidden-xs">' . $_SESSION['fullname'] . '</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="' . $_SESSION['avatar'] . '" class="img-circle" alt="User Image">
                    <p>
                      ' . $_SESSION['fullname'] . '
					  <small>' . $_SESSION['user_mail'] . '</small>
                    </p>
                  </li>

                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="account.php" class="btn btn-default btn-flat">Account</a>
                    </div>
                    <div class="pull-right">
					  <form action="login.php" method="post">
                      <input type="submit" class="btn btn-default btn-flat" value="Logout" name="logout" />
					  </form>
                    </div>
                  </li>
                </ul>
              </li>';
}

// SQLite Connection

$db = new SQLite3('database.db');

if (!$db) {
  die("db connection failed");
}

// get data from sql query
function GetData($data)
{
  $result = [];
  while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
    $result[] = $row;
  }

  if (count($result) > 0) {
    return $result;
  }

  return false;
}

// Create pagination
function SQLiteDataPagination($sqlite_query, $parameter = [])
{
  //SET LIMIT
  if (!isset($_SESSION['viewLimit'])) {
    $_SESSION['viewLimit'] = 20;
  }

  $limit = $_SESSION['viewLimit'];

  //SET Starting
  $start = (isset($_GET['page']) && is_numeric($_GET['page']) ? (($_GET['page'] - 1) * $limit) : 0);

  $page_num = (!isset($_GET['page']) ? 0 : $_GET['page']);
  if ($page_num == 0 || $page_num == "") $page_num = 1;


  $content = sqliteQuery($sqlite_query . " LIMIT $start, $limit", $parameter);

  //Start Pagination
  $params = $_GET;
  unset($params['page']);
  $params['page'] = '';
  $pHTML = '<ul class="pagination pull-right" style="margin-top:0px;">';

  $cUrl = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) . '?' . http_build_query($params);

  $totalCount = count(GetData(sqliteQuery($sqlite_query, $parameter)));

  $totalPage = ceil($totalCount / $limit);

  $pHTML .= '<li ' . ($page_num == 1 ? 'class="page-item disabled"' : '') . '><a class="page-link" href="' . $cUrl . ($page_num > 1 ? ($page_num - 1) : '#') . '">Prev</a></li>';
  $pHTML .= '<li' . ($page_num == 1 ? ' class="page-item active"' : '') . '><a class="page-link"  href="' . $cUrl . '1">1</a></li>';
  $pHTML .= ($page_num > 4 ? '<li class="page-item disabled"><a class="page-link">...</a></li>' : '');
  $startLoop = ($page_num > 4 ? ($page_num - 2) : 2);
  $endLoop = ($page_num < ($totalPage - 3) ? ($page_num + 2) : ($totalPage - 1));
  for ($i = $startLoop; $i <= $endLoop; $i++) {
    $pHTML .= '<li' . ($i == $page_num ? ' class="page-item active"' : '') . '><a class="page-link" href="' . $cUrl . $i . '">' . $i . '</a></li>';
  }
  $pHTML .= ($page_num < ($totalPage - 3) ? '<li class="page-item disabled"><a class="page-link">...</a></li>' : '');
  $pHTML .= ($totalPage > 1 ? '<li' . ($i == $page_num ? ' class="active"' : '') . '><a class="page-link" href="' . $cUrl . $totalPage . '">' . $totalPage . '</a></li>' : '');
  $pHTML .= '<li ' . ($page_num == $totalPage ? 'class="page-item disabled"' : '') . '><a class="page-link" href="' . $cUrl . ($page_num < $totalPage ? ($page_num + 1) : '#') . '">Next</a></li>';
  $pHTML .= '</ul>';

  $info = 'Showing ' . ((($page_num - 1) * $_SESSION['viewLimit']) + 1) . ' to ' . (($page_num * $_SESSION['viewLimit']) > $totalCount ? $totalCount : ($page_num * $_SESSION['viewLimit'])) . ' of ' . $totalCount;
  //Start Paginate Info

  if ($_SESSION['viewLimit'] == 'all') {
    $pHTML = '';
    $info = "Showing " . $totalCount;
  }

  return ["data" => GetData($content), "pagination" => $pHTML, "info" => $info];
}

// tree generator
function show_tree($table_name, $required = false, $conditions = "")
{
  global $con;
  // get table data
  $query = mysqli_query($con, "SELECT * FROM $table_name $conditions");

  if (mysqli_num_rows($query)) {
    // fetch all rows
    $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $parent = [];
    // build up the category tree array
    foreach ($rows as $row) {
      // sub item we are looking for
      if ($row['parent_id'] != 0) {
        $id = $row['parent_id'];
        $parent[$id]['sub'][] = $row;
      } else {
        // main parent
        $id = $row['id'];
        $parent[$id] = $row;
      }
    }
    // generate the output
    echo "<select  type='select'  name='$table_name' class='form-control' " . ($required ? 'required' : '') . "><option value=''>- Select One -</option>";
    foreach ($parent as $item) {
      echo "<optgroup label=$item[name]>";
      if ($item['sub']) {
        foreach ($item['sub'] as $subitem) {
          echo "<option value='$subitem[id]'>$subitem[name]</option>";
        }
      }
      echo "</optgroup>";
    }
    echo "</select>";
  }
}

// Reusable function for executing prepared statements with question mark placeholders
function sqliteQuery($query, $params = [])
{
  global $db;
  $stmt = $db->prepare($query);

  if ($stmt) {

    $paramCount = count($params);

    if ($paramCount > 0) {

      for ($i = 1; $i <= $paramCount; $i++) {
        $stmt->bindValue($i, $params[$i - 1]);
      }
    }
    $result = $stmt->execute();
    return $result;

    $stmt->close();
  } else {

    return false; // Failed to prepare the statement

  }
}

function setMessage($type, $message)
{

  $type = $type == "error" ? "alert-danger" : "alert-success";
  $_SESSION['message'] =  '<div class="alert ' . $type . ' alert-dismissible font15"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $message . '</div>';
}

function getMessage()
{
  if (isset($_SESSION['message'])) {

    echo $_SESSION['message'];

    unset($_SESSION['message']);
  }
}
