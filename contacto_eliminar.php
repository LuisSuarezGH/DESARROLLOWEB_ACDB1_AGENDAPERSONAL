
<?php

require_once "config/database.php";
require_once "includes/auth.php";

verificarSesion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

verificarToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: dashboard.php");
    exit;
}

// Eliminar únicamente si pertenece al usuario
$consulta = $conexion->prepare(
    "DELETE FROM contactos
     WHERE id = ? AND id_usuario = ?"
);

$consulta->execute([
    $id,
    $_SESSION['usuario_id']
]);

header("Location: dashboard.php");
exit;
