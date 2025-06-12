<?php
include_once('bd/funcion.php');

if (isset($_COOKIE["barra"])) {
  $barra = $_COOKIE["barra"];
} else {

  $barra = "";
  setcookie("barra", $barra, time() + 604800, "/");
}



?>
<script>
  function funcion() {

    valor = $('#barra').val();

    if (valor == "") {
      $('#barra').val("sidebar-collapse")
      document.cookie = "barra=sidebar-collapse;max-age=3600;path=/";
      //$.cookie.set('barra',"sidebar-callapse");

    } else {
      $('#barra').val("")
      document.cookie = "barra=;max-age=3600;path=/";
      //$.cookie.set('barra','');

    }


  };
</script>

<body class="hold-transition sidebar-mini <?php echo $barra ?> ">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <button class="nav-link btn btn-sm" style="border:none;background:none;" onclick="funcion()" data-widget="pushmenu" value="<?php echo $barra ?>" id="barra" href="#"><i class="fas fa-bars"></i></button>
          <!--<a class="nav-link" data-widget="pushmenu" id="barra" href="#" role="button"><i class="fas fa-bars"></i></a>-->
        </li>



      </ul>

      <!-- SEARCH FORM -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-sm-inline-block">
          <strong><a class="nav-link" style="font-size:x-large; color:white"  href="index.php">INERVA Terapia Cognitivo Conductual</a></strong>
        </li>


      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 medium"><?php echo fechaC(); ?></span>
          </a>
        </li>
        <!-- Nav Item - Search Dropdown (Visible Only XS) -->

        <!-- Dropdown - Messages -->


        <div class="topbar-divider d-none d-sm-block"></div>
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['s_nombre']; ?></span>
            <input type="hidden" id="rolusuario" name="rolusuario" value=<?php echo $_SESSION['s_rol'] ?>>
            <i class="fas fa-user"></i>
          </a>
          <!-- Dropdown - User Information -->
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="bd/logout.php" data-toggle="modal" data-target="#logoutModal">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Salir
            </a>

        </li>
        <!-- Notifications Dropdown Menu -->


      </ul>
    </nav>
    <!-- /.navbar -->