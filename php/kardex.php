<?php

// Conexión a la base de datos
require_once __DIR__ . "/conexion.php";

// Importación del modelo Kardex (maneja consultas SQL)
require_once __DIR__ . "/../models/Kardex.php";

// Captura la acción enviada por GET (si no existe, queda vacío)
$action = $action ?? $_GET['action'] ?? '';

/*
--------------------------------------------------
REGISTRAR MOVIMIENTO (ENTRADA / SALIDA)
--------------------------------------------------
Permite registrar movimientos en el kardex:
- entrada: ingreso de productos al stock
- salida: salida de productos del stock
*/
if($action == 'registrar'){

    // Datos recibidos del formulario
    $tipo = $_POST['tipo'];
    $id_producto = intval($_POST['id_producto']);
    $id_almacen = intval($_POST['id_almacen']);
    $cantidad = intval($_POST['cantidad']);
    $valor = floatval($_POST['valor']);

    try{

        // Si es entrada de productos
        if($tipo == 'entrada'){

            Kardex::registrarEntrada(
                $conexion,
                $id_producto,
                $id_almacen,
                $cantidad,
                $valor
            );

            $msg = 'entrada';

        }else{

            // Obtener stock actual antes de salida
            $stock = Kardex::obtenerStock(
                $conexion,
                $id_producto,
                $id_almacen
            );

            // Validar que haya suficiente stock
            if($cantidad > $stock){

                header("Location: ../index.php?vista=kardex&msg=sin_stock");
                exit();

            }

            // Registrar salida de productos
            Kardex::registrarSalida(
                $conexion,
                $id_producto,
                $id_almacen,
                $cantidad,
                $valor
            );

            $msg = 'salida';
        }

        // Redirección con mensaje de éxito
        header("Location: ../index.php?vista=kardex&msg=$msg");

    }catch(mysqli_sql_exception $e){

        // Si ocurre un error en la base de datos
        header("Location: ../index.php?vista=kardex&msg=error");
    }


/*
--------------------------------------------------
🟡 LISTAR MOVIMIENTOS DEL KARDEX
--------------------------------------------------
Obtiene el historial de movimientos con paginación
*/
}else if($action == 'listar'){

    $por_pagina = 10;

    // Página actual (si no existe, se usa 1)
    if(!isset($pagina_actual)) $pagina_actual = 1;
    if($pagina_actual < 1) $pagina_actual = 1;

    $offset = ($pagina_actual - 1) * $por_pagina;

    // Obtener movimientos del kardex
    $resultado = Kardex::listarMovimientos(
        $conexion,
        $offset,
        $por_pagina
    );

    // Total de páginas para paginación
    $total_paginas = Kardex::totalPaginas(
        $conexion,
        $por_pagina
    );

    return [
        'movimientos' => $resultado,
        'total_paginas' => $total_paginas
    ];


/*
--------------------------------------------------
 FORMULARIO (PRODUCTOS + ALMACENES)
--------------------------------------------------
Carga datos necesarios para el formulario
*/
}else if($action == 'formulario'){

    // Lista de productos disponibles
    $productos = Kardex::obtenerProductos($conexion);

    // Lista de almacenes disponibles
    $almacenes = Kardex::obtenerAlmacenes($conexion);

    return [
        'productos' => $productos,
        'almacenes' => $almacenes
    ];


/*
--------------------------------------------------
STOCK GENERAL
--------------------------------------------------
Obtiene el stock actual de todos los productos
*/
}else if($action == 'stock'){

    return Kardex::listarStock($conexion);
}

?>