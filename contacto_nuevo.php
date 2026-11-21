
<?php

require_once "config/database.php";
require_once "includes/auth.php";

verificarSesion();

$mensaje = "";

// Obtener categorías
$consulta = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
);

$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verificarToken();

    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $ciudad = trim($_POST['ciudad'] ?? '');

    $id_categoria = filter_var(
        $_POST['id_categoria'] ?? '',
        FILTER_VALIDATE_INT
    );

    $favorito = isset($_POST['favorito']) ? 1 : 0;

    $categorias_validas = array_map(
        'intval',
        array_column($categorias, 'id')
    );

    if (
        $nombre === '' ||
        strlen($nombre) > 120 ||
        !preg_match('/^[0-9+\-\s()]{7,25}$/', $telefono) ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 150 ||
        $ciudad === '' ||
        strlen($ciudad) > 100 ||
        !$id_categoria ||
        !in_array($id_categoria, $categorias_validas, true)
    ) {

        $mensaje = "Verifica los datos ingresados.";

    } else {

        $consulta = $conexion->prepare(
            "INSERT INTO contactos
            (id_usuario, nombre, telefono, correo, ciudad,
             id_categoria, favorito)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $consulta->execute([
            $_SESSION['usuario_id'],
            $nombre,
            $telefono,
            $correo,
            $ciudad,
            $id_categoria,
            $favorito
        ]);

        header("Location: dashboard.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo contacto</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">

    <h1>Registrar contacto</h1>

    <?php if ($mensaje): ?>
        <div class="alert error">
            <?= e($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?= e(generarToken()) ?>">

        <label>Nombre completo</label>
        <input type="text" name="nombre"
               maxlength="120" required
               value="<?= e($_POST['nombre'] ?? '') ?>">

        <label>Teléfono</label>
        <input type="tel" name="telefono"
               maxlength="25" required
               value="<?= e($_POST['telefono'] ?? '') ?>">

        <label>Correo electrónico</label>
        <input type="email" name="correo"
               maxlength="150" required
               value="<?= e($_POST['correo'] ?? '') ?>">

        <label>Ciudad</label>
        <input type="text" name="ciudad"
               maxlength="100" required
               value="<?= e($_POST['ciudad'] ?? '') ?>">

        <label>Categoría</label>
        <select name="id_categoria" required>

            <option value="">Seleccionar categoría</option>

            <?php foreach ($categorias as $categoria): ?>
                <option
                    value="<?= (int)$categoria['id'] ?>"
                    <?= (($_POST['id_categoria'] ?? '') == $categoria['id'])
                        ? 'selected' : '' ?>
                >
                    <?= e($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>
            <input type="checkbox" name="favorito" value="1"
                <?= isset($_POST['favorito']) ? 'checked' : '' ?>>
            Marcar como favorito
        </label>

        <button type="submit">Guardar contacto</button>

    </form>

    <a href="dashboard.php">Volver al panel</a>

</div>

</body>
</html>
