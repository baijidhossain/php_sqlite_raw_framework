<ul class="sidebar-menu">

  <li class="header">Navigation</li>
  <li id="index">
    <a href="index.php">
      <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></i>
    </a>
  </li>

  <?php if ($_SESSION['group'] == "Admin") { ?>

    <li id="user_manager">
      <a href="user_manager.php">
        <i class="fa fa-users"></i> <span>Manage Users</span>
      </a>
    </li>
  <?php } ?>

  <li id="account"><a href="account.php"> <i class="fa fa-user"></i> <span>My Account</span> </a></li>

</ul>

<script>
  var lastSegment = window.location.href.replace(/.*\//, '').split('.');
  var pagelink = document.getElementById(lastSegment[0]);
  if (typeof(pagelink) != 'undefined' && pagelink != null) {
    document.getElementById(lastSegment[0]).className = "active";
    pagelink.getElementsByTagName('a')[0].href = "#";
  }
  if (!window.location.href.includes('.php')) {
    document.getElementById('index').className = "active";
    document.getElementById('index').href = "#";
  }

  if (lastSegment[0].includes('_')) {
    var treeid = document.getElementById(lastSegment[0].split('_')[0]);
    if (typeof(treeid) != 'undefined' && treeid != null) {
      document.getElementById(treeid).classList.add("active");
    }
  }
</script>