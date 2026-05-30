<?php

class Kardex {

    public static function obtenerStock($conexion, $id_producto, $id_almacen){
        $stmt = mysqli_prepare($conexion, "
            SELECT COALESCE((
                SELECT saldo_total
                FROM tb_kardex
                WHERE id_producto = ?
                AND id_almacen = ?
                ORDER BY id_kardex DESC
                LIMIT 1
            ),0) as stock
        ");
        mysqli_stmt_bind_param($stmt, "ii", $id_producto, $id_almacen);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        return $data['stock'];
    }

    public static function registrarEntrada($conexion, $id_producto, $id_almacen, $cantidad, $valor){
        $stmt = mysqli_prepare($conexion, "CALL sp_entrada(?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiid", $id_producto, $id_almacen, $cantidad, $valor);
        return mysqli_stmt_execute($stmt);
    }

    public static function registrarSalida($conexion, $id_producto, $id_almacen, $cantidad, $valor){
        $stmt = mysqli_prepare($conexion, "CALL sp_salida(?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiid", $id_producto, $id_almacen, $cantidad, $valor);
        return mysqli_stmt_execute($stmt);
    }

    public static function listarMovimientos($conexion, $offset, $por_pagina, $buscar = ''){
        $resultado = [];
        $where = $buscar != '' ? "AND p.modelo LIKE ?" : "";
        $stmt = mysqli_prepare($conexion, "
            SELECT k.id_kardex, p.modelo, k.id_tipooperacion, k.cantidad, k.saldo_total, k.valor_unico_historico, k.create_at
            FROM tb_kardex k
            INNER JOIN tb_productos p ON k.id_producto = p.id_producto
            WHERE 1=1 $where
            ORDER BY k.id_kardex DESC
            LIMIT ? OFFSET ?
        ");
        if($buscar != ''){
            $buscar_param = "%$buscar%";
            mysqli_stmt_bind_param($stmt, "sii", $buscar_param, $por_pagina, $offset);
        }else{
            mysqli_stmt_bind_param($stmt, "ii", $por_pagina, $offset);
        }
        mysqli_stmt_execute($stmt);
        $query = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($query)){
            $resultado[] = $row;
        }
        return $resultado;
    }

    public static function totalPaginas($conexion, $por_pagina, $buscar = ''){
        if($buscar != ''){
            $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM tb_kardex k INNER JOIN tb_productos p ON k.id_producto = p.id_producto WHERE p.modelo LIKE ?");
            $buscar_param = "%$buscar%";
            mysqli_stmt_bind_param($stmt, "s", $buscar_param);
        }else{
            $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) as total FROM tb_kardex");
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $total = mysqli_fetch_assoc($result)['total'];
        return ceil($total / $por_pagina);
    }

    public static function obtenerProductos($conexion){
        $productos = [];
        $query = mysqli_query($conexion, "SELECT id_producto, modelo, precio_actual FROM tb_productos WHERE inactive_at IS NULL");
        while($row = mysqli_fetch_assoc($query)){
            $productos[] = $row;
        }
        return $productos;
    }

    public static function obtenerAlmacenes($conexion){
        $almacenes = [];
        $query = mysqli_query($conexion, "SELECT id_almacen, nombre_almacen FROM tb_almacen");
        while($row = mysqli_fetch_assoc($query)){
            $almacenes[] = $row;
        }
        return $almacenes;
    }

    public static function listarStock($conexion){
        $resultado = [];
        $query = mysqli_query($conexion, "
            SELECT a.nombre_almacen, p.modelo,
                COALESCE((
                    SELECT saldo_total FROM tb_kardex
                    WHERE id_producto = p.id_producto
                    AND id_almacen = a.id_almacen
                    ORDER BY id_kardex DESC LIMIT 1
                ),0) as stock
            FROM tb_almacen a
            CROSS JOIN tb_productos p
            WHERE p.inactive_at IS NULL
            ORDER BY a.nombre_almacen, p.modelo
        ");
        while($row = mysqli_fetch_assoc($query)){
            $resultado[] = $row;
        }
        return $resultado;
    }
}
?>