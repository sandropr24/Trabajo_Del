<?php

// Conexión a la base de datos
include("conexion.php");

// Importación del modelo Venta (lógica de negocio)
require_once __DIR__ . "/../models/Venta.php";

// Desactiva reportes automáticos de errores MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

// Captura la acción enviada por GET
$action = $action ?? $_GET['action'] ?? '';

/*
--------------------------------------------------
VENTA SIMPLE (1 PRODUCTO)
--------------------------------------------------
Registra una venta de un solo producto
*/
if($action == 'vender'){

    // Datos del formulario
    $id_producto = $_POST['id_producto'];
    $precio = $_POST['precio'];
    $modelo = $_POST['modelo'];

    // Verificar stock actual del producto
    $stock_actual = Venta::obtenerStock($conexion, $id_producto);

    // Validación de stock
    if($stock_actual < 1){
        header("Location: ../index.php?vista=ventas&msg=error");
        exit;
    }

    // Crear título de la venta
    $titulo = "Venta: $modelo (x1)";

    // Insertar venta principal
    $id_venta = Venta::insertarVenta($conexion, $titulo, $precio);

    // Insertar detalle de la venta
    Venta::insertarDetalle($conexion, $id_venta, $id_producto, 1, $precio);

    // Registrar salida en el kardex
    Venta::salidaKardex($conexion, $id_producto, 1, $precio);

    header("Location: ../index.php?vista=ventas&msg=vendido");
    exit;


/*
--------------------------------------------------
VENTA CON CARRITO (MÚLTIPLES PRODUCTOS)
--------------------------------------------------
Permite vender varios productos en una sola compra
*/
}else if($action == 'vender_carrito'){

    // Respuesta en formato JSON
    header('Content-Type: application/json');

    // Obtener carrito enviado desde JavaScript
    $carrito = json_decode(file_get_contents('php://input'), true);

    // Validar carrito vacío
    if(empty($carrito)){
        echo json_encode(['status' => 'error']);
        exit;
    }

    /*
    --------------------------------------------------
    VALIDACIÓN DE STOCK
    --------------------------------------------------
    Verifica que todos los productos tengan stock suficiente
    */
    foreach($carrito as $item){

        $stock = Venta::obtenerStock($conexion, $item['id']);

        if($item['cantidad'] > $stock){
            echo json_encode(['status' => 'error']);
            exit;
        }
    }

    $total = 0;

    // Crear título de venta con todos los productos
    $titulo_items = array_map(function($item){
        return $item['modelo'] . " (x" . $item['cantidad'] . ")";
    }, $carrito);

    $titulo = "Venta: " . implode(", ", $titulo_items);

    // Insertar venta principal
    $id_venta = Venta::insertarVenta($conexion, $titulo, $total);

    /*
    --------------------------------------------------
    REGISTRAR DETALLES Y SALIDA DE STOCK
    --------------------------------------------------
    */
    foreach($carrito as $item){

        $subt = $item['precio'] * $item['cantidad'];
        $total += $subt;

        // Detalle de venta
        Venta::insertarDetalle(
            $conexion,
            $id_venta,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        );

        // Descontar stock en kardex
        Venta::salidaKardex(
            $conexion,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        );
    }

    // Actualizar total final de la venta
    mysqli_query($conexion, "
        UPDATE tb_ventas SET total = $total WHERE id_venta = $id_venta
    ");

    echo json_encode(['status' => 'success']);
    exit;


/*
--------------------------------------------------
LISTAR PRODUCTOS PARA VENTAS
--------------------------------------------------
Muestra productos con paginación para vender
*/
}else if($action == 'listar'){

    $limit = 8;
    $pagina = $_GET['pagina'] ?? 1;
    $offset = ($pagina - 1) * $limit;

    $data = [
        'productos' => Venta::listarProductos($conexion, $limit, $offset),
        'total_paginas' => ceil(Venta::contarProductos($conexion) / $limit)
    ];

    return $data;
}

?>