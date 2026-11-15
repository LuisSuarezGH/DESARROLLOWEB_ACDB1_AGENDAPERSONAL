
<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Verificar si existe una sesión
function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Proteger la salida HTML
function e($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Generar token CSRF
function generarToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

// Comprobar token CSRF
function verificarToken() {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit("Solicitud no válida.");
    }
}
