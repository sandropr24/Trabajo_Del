<?php

class Venta
{

    public static function obtenerStock($conexion, $id_producto, $id_almacen)
    {
        $stmt = mysqli_prepare($conexion, "
        SELECT saldo_total 
        FROM tb_kardex 
        WHERE id_producto = ? 
        AND id_almacen = ?
        ORDER BY id_kardex DESC 
        LIMIT 1
    ");
        mysqli_stmt_bind_param($stmt, "ii", $id_producto, $id_almacen);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['saldo_total'] : 0;
    }

    public static function insertarVenta($conexion, $titulo, $total)
    {
        $stmt = mysqli_prepare($conexion, "
            INSERT INTO tb_ventas (fecha, titulo, total, iduser_create)
            VALUES (NOW(), ?, ?, 1)
        ");
        mysqli_stmt_bind_param($stmt, "sd", $titulo, $total);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($conexion);
    }

    public static function insertarDetalle($conexion, $id_venta, $id_producto, $cantidad, $precio)
    {
        $subtotal = $cantidad * $precio;
        $stmt = mysqli_prepare($conexion, "
            INSERT INTO tb_detalle_venta 
            (id_venta, id_producto, cantidad, precio_unitario, subtotal, precio_final, iduser_create)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");
        mysqli_stmt_bind_param($stmt, "iiiddd", $id_venta, $id_producto, $cantidad, $precio, $subtotal, $subtotal);
        return mysqli_stmt_execute($stmt);
    }

    public static function salidaKardex($conexion, $id_producto, $id_almacen, $cantidad, $precio)
    {
        $stmt = mysqli_prepare($conexion, "CALL sp_salida(?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiid", $id_producto, $id_almacen, $cantidad, $precio);
        mysqli_stmt_execute($stmt);
        while (mysqli_more_results($conexion)) {
            mysqli_next_result($conexion);
        }
    }

    public static function listarProductos(
        $conexion,
        $id_almacen,
        $limit,
        $offset
    ) {
        $productos = [];

        $stmt = mysqli_prepare($conexion, "
        SELECT
            p.id_producto,
            p.modelo,
            p.precio_actual,
            p.imagen,
            COALESCE(k.saldo_total,0) AS stock
        FROM tb_productos p
        LEFT JOIN tb_kardex k
            ON k.id_kardex = (
                SELECT MAX(id_kardex)
                FROM tb_kardex
                WHERE id_producto = p.id_producto
                AND id_almacen = ?
            )
        WHERE p.inactive_at IS NULL
        LIMIT ? OFFSET ?
    ");

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $id_almacen,
            $limit,
            $offset
        );

        mysqli_stmt_execute($stmt);

        $query = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($query)) {
            $productos[] = $row;
        }

        return $productos;
    }

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
