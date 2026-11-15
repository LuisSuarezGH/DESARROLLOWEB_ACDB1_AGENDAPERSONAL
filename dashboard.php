
<?php

require_once "config/database.php";
require_once "includes/auth.php";

// Verificar autenticación
verificarSesion();

$id_usuario = $_SESSION['usuario_id'];

// Consultar información del usuario
$consulta = $conexion->prepare(
    "SELECT nombre, correo FROM usuarios WHERE id = ?"
);

$consulta->execute([$id_usuario]);
$usuario = $consulta->fetch();

if (!$usuario) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit;
}

// Consultar únicamente los contactos del usuario
$consulta = $conexion->prepare(
    "SELECT * FROM contactos
     WHERE id_usuario = ?
     ORDER BY nombre ASC"
);

$consulta->execute([$id_usuario]);

$contactos = $consulta->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

    <header class="topbar">
        <div>
            <h1>Mi Agenda Personal</h1>
            <p>Administración de contactos</p>
        </div>

        <form action="logout.php" method="POST">
            <input type="hidden" name="csrf_token"
                   value="<?= e(generarToken()) ?>">
            <button type="submit" class="btn-danger">
                Cerrar sesión
            </button>
        </form>
    </header>

    <section class="welcome">
        <h2>Bienvenido, <?= e($usuario['nombre']) ?></h2>
        <p>Correo: <?= e($usuario['correo']) ?></p>
    </section>

    <div class="section-header">
        <h2>Mis contactos</h2>

        <a href="contacto_nuevo.php" class="btn">
            + Nuevo contacto
        </a>
    </div>

    <?php if (count($contactos) > 0): ?>

        <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($contactos as $contacto): ?>

                    <tr>
                        <td><?= e($contacto['nombre']) ?></td>
                        <td><?= e($contacto['telefono']) ?></td>
                        <td><?= e($contacto['correo']) ?></td>
                        <td><?= e($contacto['ciudad']) ?></td>

                        <td class="actions">

                            <a class="btn-edit"
                               href="contacto_editar.php?id=<?= (int)$contacto['id'] ?>">
                                Editar
                            </a>

                            <form action="contacto_eliminar.php"
                                  method="POST"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este contacto?');">

                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= e(generarToken()) ?>">

                                <input type="hidden"
                                       name="id"
                                       value="<?= (int)$contacto['id'] ?>">

                                <button type="submit"
                                        class="btn-danger">
                                    Eliminar
                                </button>

                            </form>

                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

        </div>

    <?php else: ?>

        <div class="empty">
            <h3>No tienes contactos registrados</h3>
            <p>Agrega tu primer contacto para comenzar.</p>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
