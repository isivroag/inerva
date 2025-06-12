<?php
session_start();
unset($_SESSION["s_usuario"]);
unset($_SESSION["s_nombre"]);
session_destroy();

setcookie("usuario", '', time() - 100, "/");
setcookie("pass", '', time() - 100, "/");
header("Location:../index.php");
