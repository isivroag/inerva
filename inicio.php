<?php
$pagina = 'home';
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();




?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<style>
  .starchecked {
    color: rgba(255, 195, 0, 100)
  }

  .multi-line {
    white-space: normal;
    width: 250px;
  }

  .badge-nuevo {
    background-color: #28a745;
  }

  .badge-seguimiento {
    background-color: #17a2b8;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->


  <!-- Main content -->
  <section class="content ">

  </section>
  <section>
    <div class="container-fluid">

      <div class="jumbotron bg-white mb-0" style="padding: .5rem 2rem; ">
        <div class="row justify-content-center">
          <div class="col-lg-12 text-center">

            <br>
            <img src="img/logoempresa.png" alt="" style="max-width: 200px; height: auto;">
          </div>
        </div>
      </div>
      
      <div class="card-deck">
        <div class="card text-center">
          <div class="card-header bg-green text-white">
            <h3 class="card-title">CITAS DEL DIA</h3>
          </div>
          <div class="card-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table name="tablaNuevo" id="tablaNuevo" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                      <thead class="text-center  bg-green">
                        <tr>
                          <th>ID</th>
                          <th>NOMBRE</th>
                          <th>TELÉFONO</th>
                          <th>CORREO</th>
                          <th>ASIGNADO A</th>
                          <th>FECHA REGISTRO</th>
                          <th>SEGUMIENTO</th>
                          <th>ACCIONES</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($data as $dat): ?>
                          <tr>
                            <td><?php echo $dat['id_pros'] ?></td>
                            <td><?php echo $dat['nombre'] ?></td>
                            <td><?php echo $dat['telefono'] ?></td>
                            <td><?php echo $dat['correo'] ?></td>
                            <td><?php echo $dat['nombre_col'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($dat['fecha_registro'])) ?></td>

                            <td class="text-center">
                              <?php
                              $badge_class = '';
                              $estado_text = '';
                              switch ($dat['edo_seguimiento']) {
                                case 1:
                                  $badge_class = 'badge-nuevo';
                                  $estado_text = 'Nuevo';
                                  break;
                                case 2:
                                  $badge_class = 'badge-seguimiento';
                                  $estado_text = 'Seguimiento';
                                  break;
                              }
                              ?>
                              <span class="badge <?php echo $badge_class ?>"><?php echo $estado_text ?></span>
                            </td>
                            <td></td>



                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
     
    </div>
  </section>
  <!-- /.content -->
</div>


<?php
include_once 'templates/footer.php';
?>
<script src="fjs/inicio.js?v=<?= (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-es_ES.min.js"></script>