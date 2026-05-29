<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php
    $vista = $_GET['vista'] ?? 'productos';

    switch ($vista) {
        case 'productos':
            include('view/productos_view.php');
            break;

        case 'kardex':
            include('view/kardex_view.php');
            break;
        
        case 'ventas':
            include('view/ventas_view.php');
            break;
        
        case 'historial':
            include('view/historial_view.php');
            break;

        default:
            echo "<div class='container mt-5'><div class='alert alert-danger'>Vista no encontrada</div></div>";
            break;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/apps.js"></script>
</body>
</html>