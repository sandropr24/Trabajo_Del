<?php

// Conexión a la base de datos
require_once __DIR__ . "/conexion.php";

// Importación del modelo Producto (maneja consultas SQL)
require_once __DIR__ . "/../models/Producto.php";

// Captura la acción enviada por GET
$action = $action ?? $_GET['action'] ?? '';

/*
--------------------------------------------------
INSERTAR PRODUCTO
--------------------------------------------------
Registra un nuevo producto en la base de datos
y opcionalmente guarda una imagen
*/
if($action == 'insertar'){

    // Datos del formulario
    $modelo = $_POST['modelo'];
    $precio = $_POST['precio'];
    $imagen = NULL;

    // Valores fijos (pueden ser mejorados en el futuro)
    $id_marca = 1;
    $id_tipo = 1;
    $id_unidad = 1;

    // Verificar si se subió una imagen
    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        // Obtener extensión del archivo
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

        // Generar nombre limpio para la imagen
        $nombre = strtolower(
            str_replace([' ', '"', "'", '(', ')'], ['_', '', '', '', ''], $modelo)
        ) . '.' . $extension;

        // Mover imagen a carpeta del sistema
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../img/' . $nombre);

        $imagen = $nombre;
    }

    // Insertar producto usando el modelo
    Producto::insertar(
        $conexion,
        $id_marca,
        $id_tipo,
        $id_unidad,
        $modelo,
        $precio,
        $imagen
    );

    header("Location: ../index.php?vista=productos&msg=registrado");
    exit();


/*
--------------------------------------------------
ACTUALIZAR PRODUCTO
--------------------------------------------------
Actualiza los datos básicos de un producto
*/
}else if($action == 'actualizar'){

    Producto::actualizar(
        $conexion,
        $_POST['id'],
        $_POST['modelo'],
        $_POST['precio']
    );

    header("Location: ../index.php?vista=productos&msg=actualizado");
    exit();


/*
--------------------------------------------------
 DESACTIVAR PRODUCTO
--------------------------------------------------
Oculta un producto (no lo elimina de la BD)
*/
}else if($action == 'desactivar'){

    Producto::desactivar($conexion, $_GET['id']);

    header("Location: ../index.php?vista=productos&msg=desactivado");
    exit();


/*
--------------------------------------------------
REACTIVAR PRODUCTO
--------------------------------------------------
Vuelve a activar un producto desactivado
*/
}else if($action == 'reactivar'){

    Producto::reactivar($conexion, $_GET['id']);

    header("Location: ../index.php?vista=productos&msg=reactivado");
    exit();


/*
--------------------------------------------------
LISTAR PRODUCTOS
--------------------------------------------------
Obtiene productos activos e inactivos
*/
}else if($action == 'listar'){

    return [
        'activos' => Producto::listarActivos($conexion),
        'inactivos' => Producto::listarInactivos($conexion)
    ];
}

?>