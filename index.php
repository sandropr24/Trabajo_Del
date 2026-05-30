<?php

require_once __DIR__ . '/helpers/auth.php';
iniciarSesion();

$vista = $_GET['vista'] ?? 'login';

if (!estaLogueado() && $vista !== 'login') {
    header('Location: /index.php?vista=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if (estaLogueado()): ?>
  <?php
    $rol    = rolActual();
    $nombre = $_SESSION['usuario']['nombre'];
  ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <span class="navbar-brand fw-bold">Sistema de Gestión</span>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="text-white opacity-75 small">
        <?= htmlspecialchars($nombre) ?>
        <span class="badge bg-light text-primary ms-1"><?= $rol ?></span>
      </span>
      <a href="/php/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
    </div>
  </nav>
<?php endif; ?>

<?php

switch ($vista) {

    case 'login':
        include(__DIR__ . '/view/login_view.php');
        break;

    case 'productos':
        requerirLogin();
        requerirPermisosVista('productos');
        include(__DIR__ . '/view/productos_view.php');
        break;

    case 'kardex':
        requerirLogin();
        requerirPermisosVista('kardex');
        include(__DIR__ . '/view/kardex_view.php');
        break;

    case 'ventas':
        requerirLogin();
        requerirPermisosVista('ventas');
        include(__DIR__ . '/view/ventas_view.php');
        break;

    case 'historial':
        requerirLogin();
        requerirPermisosVista('historial');
        include(__DIR__ . '/view/historial_view.php');
        break;

    default:
        echo "<div class='container mt-5'><div class='alert alert-danger'>Vista no encontrada</div></div>";
        break;
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/apps.js"></script>
</body>
</html>