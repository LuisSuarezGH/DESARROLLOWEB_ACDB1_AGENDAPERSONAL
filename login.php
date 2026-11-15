
<?php

require_once "config/database.php";
require_once "includes/auth.php";

$mensaje = "";

// Mensaje cuando el registro fue exitoso
if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso') {
    $mensaje = "Registro exitoso. Ahora puedes iniciar sesión para registrar tus contactos.";
}

// Procesar únicamente cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verificarToken();

    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 150 ||
        !is_string($password) ||
        strlen($password) > 72
    ) {

        $mensaje = "Correo o contraseña incorrectos, verifica y vuelve a intentar.";

    } else {

        $consulta = $conexion->prepare(
             "SELECT id, nombre, correo, `PASSWORD` AS password
             FROM usuarios
             WHERE correo = ?"
);

        $consulta->execute([$correo]);

        $usuario = $consulta->fetch();

        if (
            $usuario &&
            password_verify($password, $usuario['password'])
        ) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];

            header("Location: dashboard.php");
            exit;

        } else {

            $mensaje = "Correo o contraseña incorrectos.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Agenda Personal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    
    <h2>UTPL - DESARROLLO WEB</h2>
    <h1>Agenda Personal</h1>
    <p>Ingresa a tu cuenta para administrar tus contactos.</p>

    <?php if ($mensaje): ?>
        <div class="alert">
            <?= e($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?= e(generarToken()) ?>">

        <label>Correo electrónico</label>
        <input type="email" name="correo"
               maxlength="150" required>

        <label>Contraseña</label>
        <input type="password" name="password"
               maxlength="72" required>

        <button type="submit">Iniciar sesión</button>

    </form>

    <p>¿No tienes una cuenta?
        <a href="registro.php">Regístrate aquí</a>
    </p>

</div>

</body>
</html>
