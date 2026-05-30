<?php

class Producto {

    public static function insertar($conexion, $id_marca, $id_tipo, $id_unidad, $modelo, $precio, $imagen){
        $stmt = mysqli_prepare($conexion, "
            INSERT INTO tb_productos 
            (id_marca, id_tipo, id_unidad, modelo, precio_actual, SeVende, iduser_create, imagen)
            VALUES (?, ?, ?, ?, ?, '1', 1, ?)
        ");
        mysqli_stmt_bind_param($stmt, "iiisds", $id_marca, $id_tipo, $id_unidad, $modelo, $precio, $imagen);
        return mysqli_stmt_execute($stmt);
    }

    public static function actualizar($conexion, $id, $modelo, $precio){
        $stmt = mysqli_prepare($conexion, "
            UPDATE tb_productos 
            SET modelo=?, precio_actual=?
            WHERE id_producto=?
        ");
        mysqli_stmt_bind_param($stmt, "sdi", $modelo, $precio, $id);
        return mysqli_stmt_execute($stmt);
    }

    public static function desactivar($conexion, $id){
        $stmt = mysqli_prepare($conexion, "
            UPDATE tb_productos 
            SET inactive_at = NOW()
            WHERE id_producto=?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    public static function reactivar($conexion, $id){
        $stmt = mysqli_prepare($conexion, "
            UPDATE tb_productos 
            SET inactive_at = NULL
            WHERE id_producto=?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    public static function listarActivos($conexion){
        $data = [];
        $query = mysqli_query($conexion, "
            SELECT id_producto, modelo, precio_actual, imagen, create_at
            FROM tb_productos
            WHERE inactive_at IS NULL
        ");
        while($row = mysqli_fetch_assoc($query)){
            $data[] = $row;
        }
        return $data;
    }

    public static function listarInactivos($conexion){
        $data = [];
        $query = mysqli_query($conexion, "
            SELECT id_producto, modelo, precio_actual, imagen, create_at
            FROM tb_productos
            WHERE inactive_at IS NOT NULL
        ");
        while($row = mysqli_fetch_assoc($query)){
            $data[] = $row;
        }
        return $data;
    }
}
?>