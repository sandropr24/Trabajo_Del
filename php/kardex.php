<?php

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/../models/Kardex.php";
require_once __DIR__ . "/../helpers/validacion.php";

require_once __DIR__ . "/../helpers/auth.php";
iniciarSesion();

$action = $action ?? $_GET['action'] ?? '';
requerirPermisosPHP('kardex', $action);

if ($action == 'registrar') {

    $tipo        = validarTexto($_POST['tipo'], "Tipo");
    $id_producto = validarEntero($_POST['id_producto'], "Producto");
    $id_almacen  = validarEntero($_POST['id_almacen'], "Almacén");
    $cantidad    = validarNumero($_POST['cantidad'], "Cantidad", 1);
    $valor       = validarNumero($_POST['valor'], "Valor", 0);

    try {

        if ($tipo == 'entrada') {

            Kardex::registrarEntrada($conexion, $id_producto, $id_almacen, $cantidad, $valor);
            $msg = 'entrada';

        } else {

            $stock = Kardex::obtenerStock($conexion, $id_producto, $id_almacen);

            if ($cantidad > $stock) {
                // ✅ redirect absoluto
                header("Location: /index.php?vista=kardex&msg=sin_stock");
                exit();
            }

            Kardex::registrarSalida($conexion, $id_producto, $id_almacen, $cantidad, $valor);
            $msg = 'salida';
        }

        // ✅ redirect absoluto
        header("Location: /index.php?vista=kardex&msg=$msg");

    } catch (mysqli_sql_exception $e) {
        header("Location: /index.php?vista=kardex&msg=error");
    }

} else if ($action == 'listar') {

    $por_pagina = 10;

    if (!isset($pagina_actual)) $pagina_actual = 1;
    if ($pagina_actual < 1) $pagina_actual = 1;

    $offset = ($pagina_actual - 1) * $por_pagina;

    $resultado = Kardex::listarMovimientos($conexion, $offset, $por_pagina);
    $total_paginas = Kardex::totalPaginas($conexion, $por_pagina);

    return [
        'movimientos'   => $resultado,
        'total_paginas' => $total_paginas
    ];

} else if ($action == 'formulario') {

    $productos = Kardex::obtenerProductos($conexion);
    $almacenes = Kardex::obtenerAlmacenes($conexion);

    return [
        'productos' => $productos,
        'almacenes' => $almacenes
    ];

} else if ($action == 'stock') {

    return Kardex::listarStock($conexion);
}