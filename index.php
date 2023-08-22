<?php

$pageTitle = "Dashboard";

include('include/template_header.php');

?>




<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fas fa-tachometer-alt mr-1"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content my-5">
    <div class="row">
      <div class="col-md-12">Hello World!</div>
    </div>
  </section>
</div><!-- /.content-wrapper -->

<?php include('include/footer.php'); ?>
</div><!-- ./wrapper -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php include('include/scripts.php');

?>



<script>
  $(document).on('hidden.bs.modal', function(e) {
    $(e.target).removeData('bs.modal');
  });

  $('#myModal').on('hidden.bs.modal', function() {
    $('.modal-content').html('');
  });
</script>
</body>

</html>