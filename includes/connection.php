<?php
$servername = "localhost";
$username = "IngWebUser";
$password = "Ximena_Ricardo43737";
$dbname = "ingweb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexion fallida: " . $conn->connect_error);
}
?>