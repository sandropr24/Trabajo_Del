<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$_GET['pagina'] = $pagina_actual;
$action = 'listar';
$data = include("php/ventas.php");
$productos = $data['productos'];
$total_paginas = $data['total_paginas'];
?>

<div class="container-fluid px-5 mt-5">
  <div id="alerta" class="alert d-none mt-3"></div>

  <div class="row mb-4 align-items-center">
    <div class="col-md-4">
      <h1 class="text-primary m-0 fw-bold">Ventas</h1>
    </div>
    <div class="col-md-4">
      <input type="text" id="buscador" class="form-control" placeholder="Buscar producto por modelo...">
    </div>
    <div class="col-md-4 text-end">
      <a href="index.php?vista=productos" class="btn btn-primary btn-sm">Ver Productos</a>
      <a href="index.php?vista=kardex" class="btn btn-primary btn-sm">Ver Kardex</a>
      <a href="index.php?vista=historial" class="btn btn-primary btn-sm">Ver Historial</a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="row g-3" id="contenedor-productos">
        <?php foreach($productos as $row):
          $modeloEscapado = htmlspecialchars($row['modelo'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="col-md-3 tarjeta-producto" data-modelo="<?=strtolower($modeloEscapado)?>">
          <div class="card shadow border-0 h-100 text-center p-3">
            <img src="img/<?=$row['imagen']?>" class="card-img-top rounded mb-3" style="height:150px; object-fit:contain; background:#f8f9fa;">
            <h6 class="fw-bold text-dark text-truncate" title="<?=$modeloEscapado?>"><?=$modeloEscapado?></h6>
            <p class="text-primary fw-bold mb-1">S/. <?=$row['precio_actual']?></p>
            <p class="text-muted mb-2">Stock: <?=$row['stock']?></p>
            <button class="btn btn-primary btn-sm w-100 mt-auto"
              <?=$row['stock'] == 0 ? 'disabled' : ''?>
              onclick="agregarAlCarrito(<?=$row['id_producto']?>, '<?=$modeloEscapado?>', <?=$row['precio_actual']?>, <?=$row['stock']?>)">
              <?=$row['stock'] == 0 ? 'Sin stock' : 'Agregar al carrito'?>
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if($total_paginas > 1): ?>
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?=$pagina_actual == $i ? 'active' : ''?>">
              <a class="page-link" href="index.php?vista=ventas&p=<?=$i?>"><?=$i?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <div class="card shadow border-0 p-3 mb-5" style="position: sticky; top: 20px; background: #ffffff;">
        <h4 class="text-secondary border-bottom pb-2 fw-bold">🛒 Carrito de Compras</h4>
        <div id="items-carrito" class="my-3" style="min-height: 120px; max-height: 400px; overflow-y: auto;">
          <p class="text-muted text-center my-4">El carrito está vacío.</p>
        </div>
        <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
          <span>Total:</span>
          <span class="text-success">S/. <span id="total-carrito">0.00</span></span>
        </div>
        <button class="btn btn-success w-100 mt-3 fw-bold py-2" id="btn-procesar-venta" onclick="procesarVenta()" disabled>
          Realizar Venta
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/apps.js"></script>
</body>
</html>