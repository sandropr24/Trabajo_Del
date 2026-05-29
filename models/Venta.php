<?php

/*
--------------------------------------------------
MODELO VENTA
--------------------------------------------------
Este modelo maneja toda la lógica relacionada a:
- ventas
- detalles de venta
- control de stock (kardex)
- listado de productos para vender
*/

class Venta
{

    /*
    --------------------------------------------------
    OBTENER STOCK ACTUAL
    --------------------------------------------------
    Obtiene el último stock registrado de un producto
    */
    public static function obtenerStock($conexion, $id_producto)
    {

        $query = mysqli_query($conexion, "
            SELECT saldo_total 
            FROM tb_kardex 
            WHERE id_producto = $id_producto 
            ORDER BY id_kardex DESC 
            LIMIT 1
        ");

        $row = mysqli_fetch_assoc($query);

        return $row ? $row['saldo_total'] : 0;
    }


    /*
    --------------------------------------------------
    INSERTAR VENTA
    --------------------------------------------------
    Registra la venta principal en la tabla tb_ventas
    */
    public static function insertarVenta($conexion, $titulo, $total)
    {

        mysqli_query($conexion, "
            INSERT INTO tb_ventas (fecha, titulo, total, iduser_create)
            VALUES (NOW(), '$titulo', $total, 1)
        ");

        return mysqli_insert_id($conexion);
    }


    /*
    --------------------------------------------------
    INSERTAR DETALLE DE VENTA
    --------------------------------------------------
    Registra los productos incluidos en la venta
    */
    public static function insertarDetalle($conexion, $id_venta, $id_producto, $cantidad, $precio)
    {

        $subtotal = $cantidad * $precio;

        return mysqli_query($conexion, "
            INSERT INTO tb_detalle_venta 
            (id_venta, id_producto, cantidad, precio_unitario, subtotal, precio_final, iduser_create)
            VALUES 
            ($id_venta, $id_producto, $cantidad, $precio, $subtotal, $subtotal, 1)
        ");
    }


    /*
    --------------------------------------------------
    REGISTRAR SALIDA EN KARDEX
    --------------------------------------------------
    Descuenta el stock cuando se realiza una venta
    */
    public static function salidaKardex($conexion, $id_producto, $cantidad, $precio)
    {

        mysqli_query($conexion, "
            CALL sp_salida($id_producto, 1, $cantidad, $precio)
        ");

        // Limpia resultados del procedimiento almacenado
        while (mysqli_more_results($conexion)) {
            mysqli_next_result($conexion);
        }
    }


    /*
    --------------------------------------------------
    LISTAR PRODUCTOS PARA VENTAS
    --------------------------------------------------
    Muestra productos disponibles con stock
    */
    public static function listarProductos($conexion, $limit, $offset)
    {

        $productos = [];

        $query = mysqli_query($conexion, "
            SELECT p.id_producto, p.modelo, p.precio_actual, p.imagen,
            COALESCE(k.saldo_total, 0) as stock
            FROM tb_productos p
            LEFT JOIN tb_kardex k 
                ON k.id_kardex = (
                    SELECT MAX(id_kardex) 
                    FROM tb_kardex 
                    WHERE id_producto = p.id_producto
                )
            WHERE p.inactive_at IS NULL
            LIMIT $limit OFFSET $offset
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $productos[] = $row;
        }

        return $productos;
    }


    /*
    --------------------------------------------------
    CONTAR PRODUCTOS
    --------------------------------------------------
    Devuelve el total de productos activos
    (para paginación)
    */
    public static function contarProductos($conexion)
    {

        $query = mysqli_query($conexion, "
            SELECT COUNT(*) as total 
            FROM tb_productos 
            WHERE inactive_at IS NULL
        ");

        return mysqli_fetch_assoc($query)['total'];
    }
}
?>