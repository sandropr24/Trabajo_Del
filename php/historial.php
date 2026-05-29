<?php

// Conexión a la base de datos
include("conexion.php");

// Desactiva reportes de errores de MySQLi (manejo manual de errores)
mysqli_report(MYSQLI_REPORT_OFF);

// Captura la acción enviada por GET (si no existe, queda vacío)
$action = $action ?? $_GET['action'] ?? '';


/*
--------------------------------------------------
LISTAR VENTAS
--------------------------------------------------
Obtiene todas las ventas registradas en la base de datos
ordenadas de forma descendente (última venta primero)
*/
if($action == 'listar'){

    $ventas = [];

    $query = mysqli_query($conexion, "
        SELECT id_venta, fecha, titulo, total, iduser_create 
        FROM tb_ventas 
        ORDER BY id_venta DESC
    ");

    // Recorre los resultados y los guarda en un array
    while($row = mysqli_fetch_assoc($query)){
        $ventas[] = $row;
    }

    // Retorna el listado de ventas
    return $ventas;


/*
--------------------------------------------------
DETALLE DE VENTA
--------------------------------------------------
Obtiene el detalle de una venta específica
incluyendo producto, cantidad, precio y subtotal
*/
}else if($action == 'detalle'){

    // ID de la venta seleccionada
    $id_venta = $_GET['id_venta'];

    // Respuesta en formato JSON para uso con JavaScript
    header('Content-Type: application/json');

    $detalle = [];

    $query = mysqli_query($conexion, "
        SELECT 
            d.cantidad, 
            d.precio_unitario, 
            d.subtotal, 
            p.modelo 
        FROM tb_detalle_venta d 
        INNER JOIN tb_productos p 
            ON d.id_producto = p.id_producto 
        WHERE d.id_venta = $id_venta
    ");

    // Guardar cada fila en el array de detalle
    while($row = mysqli_fetch_assoc($query)){
        $detalle[] = $row;
    }

    // Retornar el detalle en formato JSON
    echo json_encode($detalle);
    exit;

}

?>