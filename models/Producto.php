<?php

/*
--------------------------------------------------
MODELO PRODUCTO
--------------------------------------------------
Este modelo maneja todas las operaciones relacionadas
a los productos del sistema (CRUD completo)
*/

class Producto {


    /*
    --------------------------------------------------
    INSERTAR PRODUCTO
    --------------------------------------------------
    Registra un nuevo producto en la base de datos
    */
    public static function insertar($conexion, $id_marca, $id_tipo, $id_unidad, $modelo, $precio, $imagen){

        return mysqli_query($conexion, "
            INSERT INTO tb_productos 
            (id_marca, id_tipo, id_unidad, modelo, precio_actual, SeVende, iduser_create, imagen)
            VALUES 
            ($id_marca, $id_tipo, $id_unidad, '$modelo', '$precio', '1', 1, '$imagen')
        ");
    }


    /*
    --------------------------------------------------
    ACTUALIZAR PRODUCTO
    --------------------------------------------------
    Modifica el nombre y precio del producto
    */
    public static function actualizar($conexion, $id, $modelo, $precio){

        return mysqli_query($conexion, "
            UPDATE tb_productos 
            SET modelo='$modelo', precio_actual='$precio'
            WHERE id_producto='$id'
        ");
    }


    /*
    --------------------------------------------------
    DESACTIVAR PRODUCTO
    --------------------------------------------------
    Oculta el producto sin eliminarlo de la base de datos
    */
    public static function desactivar($conexion, $id){

        return mysqli_query($conexion, "
            UPDATE tb_productos 
            SET inactive_at = NOW()
            WHERE id_producto='$id'
        ");
    }


    /*
    --------------------------------------------------
    REACTIVAR PRODUCTO
    --------------------------------------------------
    Vuelve a activar un producto desactivado
    */
    public static function reactivar($conexion, $id){

        return mysqli_query($conexion, "
            UPDATE tb_productos 
            SET inactive_at = NULL
            WHERE id_producto='$id'
        ");
    }


    /*
    --------------------------------------------------
    LISTAR PRODUCTOS ACTIVOS
    --------------------------------------------------
    Muestra solo productos disponibles para venta
    */
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


    /*
    --------------------------------------------------
    LISTAR PRODUCTOS INACTIVOS
    --------------------------------------------------
    Muestra productos desactivados (historial)
    */
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