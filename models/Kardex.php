<?php

/*
--------------------------------------------------
MODELO KARDEX
--------------------------------------------------
Este modelo maneja toda la lógica relacionada al
control de inventario (entradas, salidas y stock)
*/

class Kardex
{

    /*
    --------------------------------------------------
    OBTENER STOCK ACTUAL
    --------------------------------------------------
    Obtiene el último stock registrado de un producto
    en un almacén específico
    */
    public static function obtenerStock($conexion, $id_producto, $id_almacen)
    {

        $query = mysqli_query($conexion, "
            SELECT COALESCE((
                SELECT saldo_total
                FROM tb_kardex
                WHERE id_producto = $id_producto
                AND id_almacen = $id_almacen
                ORDER BY id_kardex DESC
                LIMIT 1
            ),0) as stock
        ");

        $data = mysqli_fetch_assoc($query);

        return $data['stock'];
    }


    /*
    --------------------------------------------------
    REGISTRAR ENTRADA DE PRODUCTOS
    --------------------------------------------------
    Aumenta el stock en el kardex mediante un procedimiento almacenado
    */
    public static function registrarEntrada($conexion, $id_producto, $id_almacen, $cantidad, $valor)
    {

        return mysqli_query(
            $conexion,
            "CALL sp_entrada($id_producto, $id_almacen, $cantidad, $valor)"
        );
    }


    /*
    --------------------------------------------------
    REGISTRAR SALIDA DE PRODUCTOS
    --------------------------------------------------
    Reduce el stock del producto en el kardex
    */
    public static function registrarSalida($conexion, $id_producto, $id_almacen, $cantidad, $valor)
    {

        return mysqli_query(
            $conexion,
            "CALL sp_salida($id_producto, $id_almacen, $cantidad, $valor)"
        );
    }


    /*
    --------------------------------------------------
    LISTAR MOVIMIENTOS DEL KARDEX
    --------------------------------------------------
    Muestra el historial de entradas y salidas con paginación
    */
    public static function listarMovimientos($conexion, $offset, $por_pagina)
    {

        $resultado = [];

        $query = mysqli_query($conexion, "
            SELECT k.id_kardex,
                   p.modelo,
                   k.id_tipooperacion,
                   k.cantidad,
                   k.saldo_total,
                   k.valor_unico_historico,
                   k.create_at
            FROM tb_kardex k
            INNER JOIN tb_productos p
                ON k.id_producto = p.id_producto
            ORDER BY k.id_kardex DESC
            LIMIT $por_pagina OFFSET $offset
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $resultado[] = $row;
        }

        return $resultado;
    }


    /*
    --------------------------------------------------
    TOTAL DE PÁGINAS (PAGINACIÓN)
    --------------------------------------------------
    Calcula cuántas páginas existen en el kardex
    */
    public static function totalPaginas($conexion, $por_pagina)
    {

        $query = mysqli_query(
            $conexion,
            "SELECT COUNT(*) as total FROM tb_kardex"
        );

        $total = mysqli_fetch_assoc($query)['total'];

        return ceil($total / $por_pagina);
    }


    /*
    --------------------------------------------------
    LISTAR PRODUCTOS DISPONIBLES
    --------------------------------------------------
    Obtiene productos activos para el formulario
    */
    public static function obtenerProductos($conexion)
    {

        $productos = [];

        $query = mysqli_query($conexion, "
            SELECT id_producto, modelo, precio_actual
            FROM tb_productos
            WHERE inactive_at IS NULL
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $productos[] = $row;
        }

        return $productos;
    }


    /*
    --------------------------------------------------
    LISTAR ALMACENES
    --------------------------------------------------
    Obtiene todos los almacenes registrados
    */
    public static function obtenerAlmacenes($conexion)
    {

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


    /*
    --------------------------------------------------
    STOCK GENERAL POR ALMACÉN Y PRODUCTO
    --------------------------------------------------
    Muestra el stock actual de todos los productos
    en todos los almacenes
    */
    public static function listarStock($conexion)
    {

        $resultado = [];

        $query = mysqli_query($conexion, "
            SELECT a.nombre_almacen,
                   p.modelo,
                   COALESCE((
                        SELECT saldo_total
                        FROM tb_kardex
                        WHERE id_producto = p.id_producto
                        AND id_almacen = a.id_almacen
                        ORDER BY id_kardex DESC
                        LIMIT 1
                   ),0) as stock
            FROM tb_almacen a
            CROSS JOIN tb_productos p
            WHERE p.inactive_at IS NULL
            ORDER BY a.nombre_almacen, p.modelo
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $resultado[] = $row;
        }

        return $resultado;
    }
}
?>