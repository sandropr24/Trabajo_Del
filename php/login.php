<?php

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/../helpers/auth.php";

iniciarSesion();

if (estaLogueado()) {
    $vistaDefault = rolActual() === 'admin' ? 'productos' : 'ventas';
    header("Location: /index.php?vista=$vistaDefault");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php?vista=login');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header('Location: /index.php?vista=login&msg=campos_vacios');
    exit;
}

$stmt = mysqli_prepare($conexion, "
    SELECT id_usuario, nombre, email, password, rol
    FROM tb_usuarios
    WHERE email = ?
    AND inactive_at IS NULL
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario || !password_verify($password, $usuario['password'])) {
    header('Location: /index.php?vista=login&msg=credenciales');
    exit;
}

loginUsuario($usuario);

$vistaDefault = $usuario['rol'] === 'admin' ? 'productos' : 'ventas';
header("Location: /index.php?vista=$vistaDefault");
exit;