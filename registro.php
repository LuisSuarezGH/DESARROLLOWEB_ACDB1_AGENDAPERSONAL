
<?php

require_once "config/database.php";
require_once "includes/auth.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verificarToken();

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (
        $nombre === '' ||
        strlen($nombre) > 120 ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 150 ||
        strlen($password) < 8 ||
        strlen($password) > 72
    ) {
        $mensaje = "Verifica los datos. La contraseña debe tener al menos 8 caracteres.";
    } else {

        // Comprobar si el correo ya existe
        $consulta = $conexion->prepare(
            "SELECT id FROM usuarios WHERE correo = ?"
        );

        $consulta->execute([$correo]);

        if ($consulta->fetch()) {

            $mensaje = "Este correo electrónico ya está registrado.";

        } else {

            // Encriptar contraseña
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Registrar usuario
            $consulta = $conexion->prepare(
                "INSERT INTO usuarios (nombre, correo, password)
                 VALUES (?, ?, ?)"
            );

            $consulta->execute([
                $nombre,
                $correo,
                $passwordHash
            ]);

            header("Location: login.php?registro=exitoso");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Agenda Personal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">

    <h1>Crear cuenta</h1>
    <p>Regístrate para administrar tus contactos.</p>

    <?php if ($mensaje): ?>
        <div class="alert error"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?= e(generarToken()) ?>">

        <label>Nombre completo</label>
        <input type="text" name="nombre"
               maxlength="120" required>

        <label>Correo electrónico</label>
        <input type="email" name="correo"
               maxlength="150" required>

        <label>Contraseña</label>
        <input type="password" name="password"
               minlength="8" maxlength="72" required>

        <button type="submit">Registrarme</button>

    </form>

    <p>¿Ya tienes una cuenta?
        <a href="login.php">Inicia sesión</a>
    </p>

</div>

</body>
</html>
