<?php
session_start();
session_destroy();
header("Location: /ProyectoIngWeb/index.php");
exit;
?>