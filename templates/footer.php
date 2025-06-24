    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
           <div style="display:none"> BD:<?php echo bd_nombre;?></div>
        
        
            
        </div>
        
        <strong>Copyright &copy; <?php echo date('Y')?> <a href="">INERVA Terapia Cognitivo Conductual</a>.</strong> Todos los Derechos Reservados.
        <div class="float-right d-none d-sm-block">
            <b>V.</b> 1.2 Beta
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
<!--Fade Salir-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Desea Salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body alert alert-warning">
                    <h2 class="sucess d-block mx-auto">
                        <i class="far fa-question-circle "> </i></h2>
                    <span> Seleccione "Terminar", Sí esta seguro de salir del sistema.<br> Gracias.</span>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="bd/logout.php">Salir</a>
                </div>
            </div>
        </div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>


    <!-- AdminLTE App -->
    <script src="js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="js/demo.js"></script>
    </body>

    </html>