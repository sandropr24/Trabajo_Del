<?php

const PERMISOS = [
    'admin'    => ['productos', 'kardex', 'ventas', 'historial'],
    'vendedor' => ['ventas', 'historial'],
];

const PERMISOS_PHP = [
    'admin' => [
        'productos' => ['insertar', 'actualizar', 'desactivar', 'reactivar', 'listar'],
        'kardex'    => ['registrar', 'listar', 'stock', 'formulario'],
        'ventas'    => ['vender', 'vender_carrito', 'listar', 'almacenes'],
        'historial' => ['listar', 'detalle'],
    ],
    'vendedor' => [
        'ventas'    => ['vender', 'vender_carrito', 'listar', 'almacenes'],
        'historial' => ['listar', 'detalle'],
    ],
];


function iniciarSesion(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}


function estaLogueado(): bool {
    iniciarSesion();
    return isset($_SESSION['usuario']);
}


function requerirLogin(): void {
    if (!estaLogueado()) {
        header('Location: /index.php?vista=login');
        exit;
    }
}


function rolActual(): ?string {
    iniciarSesion();
    return $_SESSION['usuario']['rol'] ?? null;
}


function puedeVerVista(string $vista): bool {
    $rol = rolActual();
    if ($rol === null) return false;
    return in_array($vista, PERMISOS[$rol] ?? []);
}


function requerirPermisosVista(string $vista): void {
    if (!puedeVerVista($vista)) {
        $vistaDefault = rolActual() === 'vendedor' ? 'ventas' : 'productos';
        header("Location: /index.php?vista=$vistaDefault&msg=sin_permiso");
        exit;
    }
}


function requerirPermisosPHP(string $modulo, string $accion): void {
    if (!estaLogueado()) {
        http_response_code(401);
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
        } else {
            header('Location: /index.php?vista=login');
        }
        exit;
    }

    $rol = rolActual();
    $accionesPermitidas = PERMISOS_PHP[$rol][$modulo] ?? [];

    if (!in_array($accion, $accionesPermitidas)) {
        http_response_code(403);
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Sin permisos']);
        } else {
            header('Location: /index.php?vista=ventas&msg=sin_permiso');
        }
        exit;
    }
}


function loginUsuario(array $usuario): void {
    iniciarSesion();
    session_regenerate_id(true); 
    $_SESSION['usuario'] = [
        'id'     => $usuario['id_usuario'],
        'nombre' => $usuario['nombre'],
        'email'  => $usuario['email'],
        'rol'    => $usuario['rol'],
    ];
}


function logoutUsuario(): void {
    iniciarSesion();
    $_SESSION = [];
    session_destroy();
}