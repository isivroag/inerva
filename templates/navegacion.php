<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
  <!-- Brand Logo -->
  <a href="inicio.php" class="brand-link ">

    <img src="img/logob.png" alt="Logo" class="brand-image img-circle " style="background-color: white; ">
    <span class="brand-text font-weight-bold text-white">BIENVENIDO</span>
  </a>


  <!-- Sidebar -->
  <div class="sidebar ">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex ">
      <div class="image">
        <img src="img/usuario1.png" class="img-circle " alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['s_nombre']; ?></a>
        <input type="hidden" id="iduser" name="iduser" value="<?php echo $_SESSION['s_id_usuario']; ?>">
        <input type="hidden" id="nameuser" name="nameuser" value="<?php echo $_SESSION['s_nombre']; ?>">
        <input type="hidden" id="tipousuario" name="tipousuario" value="<?php echo $_SESSION['s_rol']; ?>">
      
        <input type="hidden" id="fechasys" name="fechasys" value="<?php echo date('Y-m-d') ?>">
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent " data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        <li class="nav-item ">
          <a href="inicio.php" class="nav-link <?php echo ($pagina == 'home') ? "active" : ""; ?> ">
            <i class="nav-icon fa-sharp-duotone fa-regular fa-house "></i>
            <p>
              Home
            </p>
          </a>
        </li>

        <?php if ($_SESSION['s_rol'] != '5') { ?>
          <!-- ABRE MENU CATALOGOS -->


          <li class="nav-item  has-treeview <?php echo ($pagina == 'paciente' ||  $pagina == 'colaborador' ||  $pagina == 'consultorio'  ) ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo ($pagina == 'paciente' || $pagina == 'colaborador' ||  $pagina == 'consultorio') ? "active" : ""; ?>">
              <i class="nav-icon  fa-sharp-duotone fa-regular fa-books "></i>
              <p>
                Catalogos
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>


            <ul class="nav nav-treeview text-white" >



              <li class="nav-item">
                <a href="cntapaciente.php" class="nav-link <?php echo ($pagina == 'paciente') ? "seleccionado" : ""; ?>  ">
                  <i class=" fa-duotone fa-regular fa-users-viewfinder nav-icon"></i>
                  <p>PACIENTES</p>
                </a>
              </li>
            
                <li class="nav-item">
                  <a href="cntacolaborador.php" class="nav-link <?php echo ($pagina == 'colaborador') ? "seleccionado" : ""; ?>  ">
                    <i class=" fa-duotone fa-regular fa-user-headset nav-icon"></i>
                    <p>COLABORADORES</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntaconsultorio.php" class="nav-link <?php echo ($pagina == 'consultorio') ? "seleccionado" : ""; ?>  ">
                    <i class=" fa-duotone fa-regular fa-user-tie nav-icon"></i>
                    <p>CONSULTORIOS</p>
                  </a>
                </li>
           



                

            </ul>

          </li>

          <!-- CIERRA MENU CATALOGOS -->

          <?php if ($_SESSION['s_rol'] == '2' || $_SESSION['s_rol'] == '3' || $_SESSION['s_rol'] == '4') { ?>
            <li class="nav-item  has-treeview <?php echo ($pagina == 'cntaseguimiento' || $pagina == 'seguimiento' || $pagina == 'cntapresupuesto' ||
                                                $pagina === 'presupuesto' || $pagina == "cntapagoordenes" || $pagina == "reportepagos") ? "menu-open" : ""; ?>">
              <a href="#" class="nav-link  <?php echo ($pagina == 'cntaseguimiento' || $pagina == 'seguimiento' || $pagina == 'cntapresupuesto' ||
                                              $pagina === 'presupuesto' || $pagina == "cntapagocxpgral" || $pagina == "reportepagos") ? "active" : ""; ?>">
                <i class="fa-sharp-duotone fa-regular fa-briefcase-blank  nav-icon"></i>
                <p>
                  Operaciones
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>


              <ul class="nav nav-treeview">





                <li class="nav-item">
                  <a href="cntahistorial.php" class="nav-link <?php echo ($pagina == 'cntaseguimiento') ? " seleccionado" : ""; ?>  ">
                    <i class="fa-sharp-duotone fa-regular fa-rectangle-history-circle-user nav-icon"></i>
                    <p>Cnta Seguimiento</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="seguimiento.php" class="nav-link <?php echo ($pagina == 'seguimiento') ? " seleccionado" : ""; ?>  ">
                    <i class="fa-sharp-duotone fa-regular fa-messages nav-icon"></i>
                    <p>Seguimiento</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntapresupuesto.php" class="nav-link <?php echo ($pagina == 'cntapresupuesto') ? " seleccionado" : ""; ?>  ">
                    <i class="fa-sharp-duotone fa-regular fa-file-spreadsheet nav-icon"></i>
                    <p>Cnta Presupuesto</p>
                  </a>
                </li>


                <?php if ($_SESSION['s_rol'] == '4' || $_SESSION['s_rol'] == '2' || $_SESSION['s_rol'] == '3')  { ?>
                  <li class="nav-item">
                    <a href="cot.php" class="nav-link <?php echo ($pagina == 'presupuesto') ? " seleccionado" : ""; ?>  ">
                      <i class="fa-sharp-duotone fa-regular fa-memo nav-icon"></i>
                      <p>Presupuesto</p>
                    </a>
                  </li>
                <?php } ?>


              </ul>

            </li>
          <?php } ?>

          <li class="nav-item  has-treeview <?php echo ($pagina == 'rptprospectos') ? "menu-open" : ""; ?>">
            <a href="#" class="nav-link  <?php echo ($pagina == 'rptprospectos') ? "active" : ""; ?>">
              <i class="fa-sharp-duotone fa-regular fa-display-chart-up nav-icon"></i>
              <p>
                Reportes
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>


            <ul class="nav nav-treeview">



              <li class="nav-item">
                <a href="rptprospectos.php" class="nav-link <?php echo ($pagina == 'rptprospectos') ? " seleccionado" : ""; ?>  ">
                  <i class="fa-sharp-duotone fa-regular fa-diagram-project nav-icon"></i>
                  <p>Est. Prospectos</p>
                </a>
              </li>




            </ul>

          </li>

          <?php if ($_SESSION['s_rol'] == '3') { ?>
            <!--
            <li class="nav-item has-treeview <?php echo ($pagina == 'cntaingresos' || $pagina == 'ingresos' || $pagina == 'cntacobros'
                                                || $pagina == 'cntacxc' || $pagina == 'recepcion' || $pagina == 'ingresos' || $pagina == 'diario' || $pagina == 'confirmar') ? "menu-open" : ""; ?>">


              <a href="#" class="nav-link <?php echo ($pagina == 'cntaingresos' || $pagina == 'ingresos' || $pagina == 'cntacobros'
                                            || $pagina == 'cntacxc' || $pagina == 'recepcion' || $pagina == 'ingresos' || $pagina == 'diario' || $pagina == 'confirmar') ? "active" : ""; ?>">

                <span class="fa-stack">
                  <i class=" fas fa-arrow-up "></i>
                  <i class=" fas fa-dollar-sign "></i>

                </span>
                <p>
                  Ingresos

                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">


                <li class="nav-item">
                  <a href="cntacxc.php" class="nav-link <?php echo ($pagina == 'cntacxc') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-list nav-icon"></i>
                    <p>Cuentas x Cobrar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntacobros.php" class="nav-link <?php echo ($pagina == 'cntacobros') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-file-invoice-dollar nav-icon"></i>
                    <p>Cobros-Det Partidas </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="cntaingresos.php" class="nav-link <?php echo ($pagina == 'cntaingresos') ? "active seleccionado" : ""; ?>  ">

                    <i class="fas text-green fa-search-dollar nav-icon"></i>
                    <p>Consulta Ingresos</p>
                  </a>
                </li>


              </ul>
            </li>


         
             -->
          <?php } ?>





        <?php } ?>




        <?php if ($_SESSION['s_rol'] == '3') { ?>
          <hr class="sidebar-divider">
          <li class="nav-item">
            <a href="cntausuarios.php" class="nav-link <?php echo ($pagina == 'usuarios') ? "active" : ""; ?> ">
              <i class="fas fa-user-shield"></i>
              <p>Usuarios</p>
            </a>
          </li>
        <?php } ?>

        <?php if ($_SESSION['s_rol'] == '5') { ?>
          <hr class="sidebar-divider">
          <li class="nav-item">
            <a href="capprospecto.php" class="nav-link <?php echo ($pagina == 'prospecto') ? "seleccionado" : ""; ?>  ">
              <i class=" fa-duotone fa-regular fa-users-viewfinder nav-icon"></i>
              <p>Prospectos</p>
            </a>
          </li>
        <?php } ?>

        <hr class="sidebar-divider">
        <li class="nav-item">
          <a class="nav-link" href="bd/logout.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <p>Salir</p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<!-- Main Sidebar Container -->