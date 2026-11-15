
<?php

$host = "localhost";
$dbname = "agenda_personal";
$username = "root";
$password = "";

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    $conexion->setAttribute(
        PDO::ATTR_EMULATE_PREPARES,
        false
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("No se pudo establecer la conexión con la base de datos.");
}
