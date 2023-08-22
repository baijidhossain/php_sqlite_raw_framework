<?php
require_once(__DIR__ . '/common.php');
require_once(__DIR__ . '/header.php');

?>

<body class="hold-transition <?= THEME; ?> sidebar-mini ">
  <!-- Site wrapper -->
  <div class="wrapper">

    <header class="main-header">
      <!-- Logo -->
      <a href="index.php" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <?php GetMLogo(); ?>
        <!-- logo for regular state and mobile devices -->
        <?php GetLogo(); ?>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
          <i class="fas fa-bars"></i>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <?php GenerateUserBox(); ?>
          </ul>
        </div>
      </nav>
    </header>

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="<?= $_SESSION['avatar'] ?? "template/user/1.png" ?>" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?php echo $_SESSION['fullname']; ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> <span class="">Online</span></a>
          </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php include('menu.php'); ?>
      </section>
      <!-- /.sidebar -->
    </aside>