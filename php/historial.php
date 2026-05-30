<?php

include(__DIR__ . "/conexion.php");

require_once __DIR__ . "/../helpers/auth.php";
iniciarSesion();

$action = $action ?? $_GET['action'] ?? '';
requerirPermisosPHP('historial', $action);

mysqli_report(MYSQLI_REPORT_OFF);


/*
--------------------------------------------------
LISTAR VENTAS
--------------------------------------------------
*/
if ($action == 'listar') {

    $ventas = [];

    $query = mysqli_query($conexion, "
        SELECT id_venta, fecha, titulo, total, iduser_create 
        FROM tb_ventas 
        ORDER BY id_venta DESC
    ");

    while ($row = mysqli_fetch_assoc($query)) {
        $ventas[] = $row;
    }

    return $ventas;


/*
--------------------------------------------------
DETALLE DE VENTA
--------------------------------------------------
*/
} else if ($action == 'detalle') {

    $id_venta = (int)$_GET['id_venta'];

    header('Content-Type: application/json');

    $detalle = [];

    $stmt = mysqli_prepare($conexion, "
        SELECT 
            d.cantidad, 
            d.precio_unitario, 
            d.subtotal, 
            p.modelo 
        FROM tb_detalle_venta d 
        INNER JOIN tb_productos p 
            ON d.id_producto = p.id_producto 
        WHERE d.id_venta = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $id_venta);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $detalle[] = $row;
    }

    echo json_encode($detalle);
    exit;

}