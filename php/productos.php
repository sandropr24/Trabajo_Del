<?php

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/../models/Producto.php";
require_once __DIR__ . "/../helpers/validacion.php";

require_once __DIR__ . "/../helpers/auth.php";
iniciarSesion();

$action = $action ?? $_GET['action'] ?? '';
requerirPermisosPHP('productos', $action);

if ($action == 'insertar') {

    $modelo = validarTexto($_POST['modelo'], "Modelo");
    $precio = validarNumero($_POST['precio'], "Precio", 0.01);
    $imagen = NULL;

    $id_marca  = 1;
    $id_tipo   = 1;
    $id_unidad = 1;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {

        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

        $nombre = strtolower(
            str_replace([' ', '"', "'", '(', ')'], ['_', '', '', '', ''], $modelo)
        ) . '.' . $extension;

        move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../img/' . $nombre);

        $imagen = $nombre;
    }

    Producto::insertar($conexion, $id_marca, $id_tipo, $id_unidad, $modelo, $precio, $imagen);

    header("Location: /index.php?vista=productos&msg=registrado");
    exit();
} else if ($action == 'actualizar') {

    $id     = validarEntero($_POST['id'], "Producto");
    $modelo = validarTexto($_POST['modelo'], "Modelo");
    $precio = validarNumero($_POST['precio'], "Precio", 0.01);

    Producto::actualizar($conexion, $id, $modelo, $precio);

    header("Location: /index.php?vista=productos&msg=actualizado");
    exit();
} else if ($action == 'desactivar') {

    $id = validarEntero($_GET['id'], "Producto");
    Producto::desactivar($conexion, $id);

    header("Location: /index.php?vista=productos&msg=desactivado");
    exit();
} else if ($action == 'reactivar') {

    $id = validarEntero($_GET['id'], "Producto");
    Producto::reactivar($conexion, $id);

    header("Location: /index.php?vista=productos&msg=reactivado");
    exit();
} else if ($action == 'listar') {

    return [
        'activos'   => Producto::listarActivos($conexion),
        'inactivos' => Producto::listarInactivos($conexion)
    ];
}
