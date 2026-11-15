
<?php

require_once "includes/auth.php";

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}

exit;
