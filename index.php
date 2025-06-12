<?php
session_start();

$inicio = 1;
if ($inicio == 0) {
    header("Location: mantenimiento.html");
    exit();
}

include_once('bd/cookie.php');

if (isset($_SESSION["s_usuario"])) {
    header("Location: inicio.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INERVA | Inicio de Sesión</title>
    
   
    <!-- Font Awesome -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesomep/css/all.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="css/adminlte.css">
    <!-- SweetAlert -->
    <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">
    
<link rel="apple-touch-icon" sizes="57x57" href="img/icon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="img/icon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="img/icon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="img/icon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="img/icon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="img/icon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="img/icon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="img/icon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="img/icon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="img/icon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="img/icon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="img/icon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/icon/favicon-16x16.png">
<link rel="manifest" href="img/icon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="img/icon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        body {
            background-color: #14320e !important /*#7c848b !important*/;
            background-color: #4d7a8c !important;
           /*background: radial-gradient(circle, #6cbad9, #b8a0fe) !important;  Degradado radial de verde oscuro a verde más oscuro */
            background: #4D7A8C !important;
            background: radial-gradient(circle,rgba(0, 0, 0, 1) 10%, rgba(184, 160, 254, 1) 81%) !important; /* Degradado radial de verde oscuro a verde más oscuro */
            background: #6CBAD9 !important;
            background: radial-gradient(circle, rgba(108, 186, 217, 1) 1%, rgba(184, 160, 254, 1) 57%) !important;
            color: white !important;

           
        }
        .login-box {
            width: 450px;
        }
        .card {
            background: white !important;
            color: #14320e !important;
            border-radius: 15px !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4) !important;
            padding: 20px !important;
        }
        .btn-primary {
            background-color: #b8a0fe !important;
            border-color: #6cbad9 !important;
            font-size: 1.2rem !important;
            padding: 10px !important;
        }
        .btn-primary:hover {
            background-color: #6cbad9 !important;
            border-color: #6cbad9 !important;
        }
        .input-group-text {
            background-color: #6cbad9   !important;
            color: white !important;
        }
        .login-logo img {
            max-width: 180px !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card shadow-lg">
            <div class="card-header text-center">
                <img src="img/logoempresa.png" alt="Logo" class="img-fluid login-logo">
                <h3 class="mt-3">Inicio de Sesión</h3>
            </div>
            <div class="card-body login-card-body">
                <form id="formlogin" action="bd/login.php" method="post">
                    <div class="mb-4">
                        <label for="username" class="form-label">Usuario</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="pass" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña" required>
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="recordar" name="recordar">
                        <label class="form-check-label" for="recordar">Recordarme</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert -->
    <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- Código personalizado -->
    <script src="fjs/codigo.js"></script>
</body>
</html>
