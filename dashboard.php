
<?php

require_once "config/database.php";
require_once "includes/auth.php";

verificarSesion();

$id_usuario = $_SESSION['usuario_id'];

// Consultar información del usuario
$consulta = $conexion->prepare(
    "SELECT nombre, correo FROM usuarios WHERE id = ?"
);

$consulta->execute([$id_usuario]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit;
}

// Obtener parámetros de búsqueda y filtros
$busqueda = trim($_GET['buscar'] ?? '');

$orden = $_GET['orden'] ?? 'nombre';

$favoritos = isset($_GET['favoritos'])
    && $_GET['favoritos'] === '1';

$id_categoria = filter_var(
    $_GET['categoria'] ?? '',
    FILTER_VALIDATE_INT
);

// Evitar órdenes SQL no permitidos
$ordenes_validos = [
    'nombre' => 'c.nombre ASC',
    'ciudad' => 'c.ciudad ASC'
];

if (!isset($ordenes_validos[$orden])) {
    $orden = 'nombre';
}

$orden_sql = $ordenes_validos[$orden];

// Consultar categorías
$consulta = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
);

$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Construir consulta de contactos
$sql = "SELECT c.*, cat.nombre AS categoria_nombre
        FROM contactos c
        INNER JOIN categorias cat
        ON c.id_categoria = cat.id
        WHERE c.id_usuario = ?";

$parametros = [$id_usuario];

// Búsqueda por nombre, correo o ciudad
if ($busqueda !== '') {

    $sql .= " AND (
        c.nombre LIKE ?
        OR c.correo LIKE ?
        OR c.ciudad LIKE ?
    )";

    $termino = "%" . $busqueda . "%";

    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
}

// Filtrar favoritos
if ($favoritos) {
    $sql .= " AND c.favorito = 1";
}

// Filtrar categoría
if ($id_categoria && $id_categoria > 0) {
    $sql .= " AND c.id_categoria = ?";
    $parametros[] = $id_categoria;
}

// Ordenar resultados
$sql .= " ORDER BY " . $orden_sql;

$consulta = $conexion->prepare($sql);
$consulta->execute($parametros);

$contactos = $consulta->fetchAll(PDO::FETCH_ASSOC);

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

    <!-- Buscador y filtros -->

    <form method="GET" class="filters">

        <label>Buscar contacto</label>

        <input
            type="search"
            name="buscar"
            placeholder="Nombre, correo o ciudad"
            value="<?= e($busqueda) ?>"
        >

        <label>Ordenar por</label>

        <select name="orden">

            <option value="nombre"
                <?= $orden === 'nombre' ? 'selected' : '' ?>>
                Nombre
            </option>

            <option value="ciudad"
                <?= $orden === 'ciudad' ? 'selected' : '' ?>>
                Ciudad
            </option>

        </select>

        <label>Categoría</label>

        <select name="categoria">

            <option value="">Todas las categorías</option>

            <?php foreach ($categorias as $categoria): ?>

                <option
                    value="<?= (int)$categoria['id'] ?>"
                    <?= $id_categoria == $categoria['id']
                        ? 'selected' : '' ?>
                >
                    <?= e($categoria['nombre']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>
            <input
                type="checkbox"
                name="favoritos"
                value="1"
                <?= $favoritos ? 'checked' : '' ?>
            >
            Mostrar solo favoritos
        </label>

        <button type="submit" class="btn">
            Aplicar filtros
        </button>

        <a href="dashboard.php" class="btn-edit">
            Limpiar filtros
        </a>

    </form>

    <!-- Lista de contactos -->

    <?php if (count($contactos) > 0): ?>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Categoría</th>
                        <th>Favorito</th>
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

                        <td><?= e($contacto['categoria_nombre']) ?></td>

                        <td>
                            <?= $contacto['favorito'] ? '★ Sí' : '☆ No' ?>
                        </td>

                        <td class="actions">

                            <a
                                class="btn-edit"
                                href="contacto_editar.php?id=<?= (int)$contacto['id'] ?>"
                            >
                                Editar
                            </a>

                            <form
                                action="contacto_eliminar.php"
                                method="POST"
                                onsubmit="return confirm('¿Estás seguro de eliminar este contacto?');"
                            >

                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= e(generarToken()) ?>">

                                <input type="hidden"
                                       name="id"
                                       value="<?= (int)$contacto['id'] ?>">

                                <button type="submit" class="btn-danger">
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

            <h3>No se encontraron contactos</h3>

            <p>
                No existen contactos que coincidan con los filtros aplicados.
            </p>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
