<?php

include(__DIR__ . "/conexion.php");
require_once __DIR__ . "/../models/Venta.php";
require_once __DIR__ . "/../helpers/validacion.php";

require_once __DIR__ . "/../helpers/auth.php";
iniciarSesion();

$action = $action ?? $_GET['action'] ?? '';
requerirPermisosPHP('ventas', $action);   

mysqli_report(MYSQLI_REPORT_OFF);


if ($action == 'vender') {

    $id_producto = validarEntero($_POST['id_producto'], "Producto");
    $precio      = validarNumero($_POST['precio'], "Precio", 0.01);
    $modelo      = validarTexto($_POST['modelo'], "Modelo");
    $id_almacen  = validarEntero($_POST['id_almacen'], "Almacén");

    $stock_actual = Venta::obtenerStock($conexion, $id_producto, $id_almacen);

    if ($stock_actual < 1) {
        header("Location: /index.php?vista=ventas&msg=error");
        exit;
    }

    mysqli_begin_transaction($conexion);

    try {

        $titulo = "Venta: $modelo (x1)";
        $id_venta = Venta::insertarVenta($conexion, $titulo, $precio);

        Venta::insertarDetalle($conexion, $id_venta, $id_producto, 1, $precio);
        Venta::salidaKardex($conexion, $id_producto, $id_almacen, 1, $precio);

        mysqli_commit($conexion);

        header("Location: /index.php?vista=ventas&msg=vendido");

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        header("Location: /index.php?vista=ventas&msg=error");
    }

    exit;
}

else if ($action == 'vender_carrito') {

    header('Content-Type: application/json');

    $body = json_decode(file_get_contents('php://input'), true);
    $carrito = $body['carrito'] ?? [];

    $id_almacen = validarEntero($body['id_almacen'] ?? null, "Almacén");

    if (empty($carrito)) {
        echo json_encode(['status' => 'error', 'message' => 'Carrito vacío']);
        exit;
    }

    foreach ($carrito as $item) {

        $id_producto = validarEntero($item['id'], "Producto");
        $cantidad    = validarNumero($item['cantidad'], "Cantidad", 1);

        $stock = Venta::obtenerStock($conexion, $id_producto, $id_almacen);

        if ($cantidad > $stock) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Stock insuficiente para ' . $item['modelo']
            ]);
            exit;
        }
    }

    mysqli_begin_transaction($conexion);

    try {

        $total = 0;

        $titulo_items = array_map(function ($item) {
            return $item['modelo'] . " (x" . $item['cantidad'] . ")";
        }, $carrito);

        $titulo = "Venta: " . implode(", ", $titulo_items);

        $id_venta = Venta::insertarVenta($conexion, $titulo, 0);

        foreach ($carrito as $item) {

            $id_producto = validarEntero($item['id'], "Producto");
            $cantidad    = validarNumero($item['cantidad'], "Cantidad", 1);
            $precio      = validarNumero($item['precio'], "Precio", 0.01);

            $subtotal = $cantidad * $precio;
            $total += $subtotal;

            Venta::insertarDetalle(
                $conexion,
                $id_venta,
                $id_producto,
                $cantidad,
                $precio
            );

            Venta::salidaKardex(
                $conexion,
                $id_producto,
                $id_almacen,
                $cantidad,
                $precio
            );
        }

        $stmt = mysqli_prepare($conexion, "
            UPDATE tb_ventas 
            SET total = ? 
            WHERE id_venta = ?
        ");

        mysqli_stmt_bind_param($stmt, "di", $total, $id_venta);
        mysqli_stmt_execute($stmt);

        mysqli_commit($conexion);

        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        mysqli_rollback($conexion);

        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la transacción'
        ]);
    }

    exit;
}

else if ($action == 'listar') {

    $limit = 8;
    $pagina = $_GET['pagina'] ?? 1;
    $offset = ($pagina - 1) * $limit;

    $id_almacen = validarEntero($_GET['id_almacen'] ?? null, "Almacén");

    return [
        'productos' => Venta::listarProductos(
            $conexion,
            $id_almacen,
            $limit,
            $offset
        ),
        'total_paginas' => ceil(
            Venta::contarProductos($conexion) / $limit
        )
    ];
}

else if ($action == 'almacenes') {

    $almacenes = [];
    $query = mysqli_query($conexion, "
        SELECT id_almacen, nombre_almacen 
        FROM tb_almacen
    ");

    while ($row = mysqli_fetch_assoc($query)) {
        $almacenes[] = $row;
    }

    return $almacenes;
}