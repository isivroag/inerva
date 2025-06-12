<?php
$pagina = "usuarios";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM w_usuario JOIN rol ON w_usuario.rol_usuario=rol.id WHERE w_usuario.edo_usuario=1 and w_usuario.id_usuario <> '". $_SESSION['s_id_usuario']."' ORDER BY w_usuario.id_usuario";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);


$consultar = "SELECT * FROM rol ORDER BY id";
$resultador = $conexion->prepare($consultar);
$resultador->execute();
$datar = $resultador->fetchAll(PDO::FETCH_ASSOC);

$message = "";



?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green">
                <div class="card-title">
                <h1 class="card-title  mx-auto">Usuarios</h1>
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-user-plus text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table name="tablaUsuario" id="tablaUsuario" class="table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%">
                                    <thead class="text-center bg-green">
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Login</th>
                                            <th>Password</th>
                                            <th>Edo</th>
                                            <th>rol_usuario</th>
                                  
                                            <th>rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($data as $dat) {
                                        ?>
                                            <tr>
                                                <td><?php echo $dat['id_usuario'] ?></td>
                                                <td><?php echo $dat['nombre'] ?></td>
                                                <td><?php echo $dat['email'] ?></td>
                                                <td><?php echo $dat['username'] ?></td>
                                                <td><?php echo $dat['password'] ?></td>
                                                <td><?php echo $dat['edo_usuario'] ?></td>
                                                <td><?php echo $dat['rol_usuario'] ?></td>
                                       
                                                <td><?php echo $dat['rol'] ?></td>


                                                <td></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
           
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>


    <section>
        <div class="modal fade" id="modalU" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">NUEVO USUARIO</h5>

                    </div>
                    <form id="formDatos" action="" method="POST">
                        <div class="modal-body row">

                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label for="nombre" class="col-form-label">Nombre:</label>
                                    <input type="text" class="form-control" name="nombre" id="nombre" autocomplete="off" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label for="username" class="col-form-label">Nombre de Usuario:</label>
                                    <input type="text" class="form-control" name="username" id="username" autocomplete="off" placeholder="Login">
                                </div>
                                <div class="form-group">
                                    <label for="pass1" class="col-form-label">Contraseña:</label>
                                    <input type="password" class="form-control" name="pass1" id="pass1" require>
                                </div>

                            </div>
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label for="email" class="col-form-label">Correo:</label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>

                                <div class="form-group auto">
                                    <label for="rol" class="col-form-label">Tipo:</label>
                                    <select class="form-control" name="rol" id="rol">
                                        <?php
                                            foreach ($datar as $dtr)
                                            {
                                        ?>
                                            <option id="<?php echo $dtr['id'] ?>" value="<?php echo $dtr['id'] ?>"><?php echo $dtr['rol'] ?></option>
                                        
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="pass2" class="col-form-label">Confirmar Contraseña:</label>
                                    <input type="password" class="form-control" name="pass2" id="pass2" require>
                                </div>
                            </div>
                        </div>


                        <?php
                        if ($message != "") {
                        ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <span class="badge "><?php echo ($message); ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>

                            </div>

                        <?php
                        }
                        ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-success" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>


<?php include_once 'templates/footer.php'; ?>
<script src="fjs/usuario.js?v=<?php echo(rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>