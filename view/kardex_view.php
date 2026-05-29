<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Kardex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php
$action = 'listar';
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$data_kardex = include("php/kardex.php");
$data = $data_kardex['movimientos'];
$total_paginas_kardex = $data_kardex['total_paginas'];

$action = 'stock';
$stock_data = include("php/kardex.php");

$action = 'formulario';
$form_data = include("php/kardex.php");
$productos = $form_data['productos'];
$almacenes = $form_data['almacenes'];
?>

<div class="container mt-5">
  <div id="alerta" class="alert d-none mt-3"></div>
  <div class="mb-4">
    <h1 class="text-primary mb-3">Kardex</h1>
    <div class="d-flex justify-content-end gap-2">
      <a href="index.php?vista=productos" class="btn btn-primary">Ver Productos</a>
      <a href="index.php?vista=ventas" class="btn btn-primary">Ver Ventas</a>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Registrar Movimiento</div>
    <div class="card-body">
      <form action="php/kardex.php?action=registrar" method="POST" class="row g-3">

        <div class="col-md-4">
          <label class="form-label">Tipo</label>
          <select name="tipo" class="form-control" required>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Producto</label>
          <select name="id_producto" id="producto" class="form-control" onchange="autocompletarPrecio()" required>
            <option value="">Seleccione</option>
            <?php foreach($productos as $p): ?>
              <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio_actual'] ?>">
                <?= $p['modelo'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Almacén</label>
          <select name="id_almacen" class="form-control" required>
            <option value="">Seleccione</option>
            <?php foreach($almacenes as $a): ?>
              <option value="<?= $a['id_almacen'] ?>">
                <?= $a['nombre_almacen'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Cantidad</label>
          <input type="number" name="cantidad" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Valor Unitario</label>
          <input type="number" step="0.01" name="valor" id="valor" class="form-control" required>
        </div>

        <div class="col-12">
          <button class="btn btn-primary w-100">Registrar</button>
        </div>

      </form>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Movimientos</div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover mb-0">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Saldo</th>
            <th>Valor Unitario</th>
            <th>Total</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <?php
            $tipo = $row['id_tipooperacion'] == 1
              ? '<span class="badge bg-success">Entrada</span>'
              : '<span class="badge bg-danger">Salida</span>';
            $total = $row['cantidad'] * $row['valor_unico_historico'];
          ?>
          <tr>
            <td><?= $row['id_kardex'] ?></td>
            <td><?= htmlspecialchars($row['modelo']) ?></td>
            <td><?= $tipo ?></td>
            <td><?= $row['cantidad'] ?></td>
            <td><?= $row['saldo_total'] ?></td>
            <td>S/. <?= number_format($row['valor_unico_historico'],2) ?></td>
            <td>S/. <?= number_format($total,2) ?></td>
            <td><?= $row['create_at'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php if($total_paginas_kardex > 1): ?>
      <nav class="mt-3 mb-3">
        <ul class="pagination justify-content-center">
          <?php for($i = 1; $i <= $total_paginas_kardex; $i++): ?>
            <li class="page-item <?=$pagina_actual == $i ? 'active' : ''?>">
              <a class="page-link" href="index.php?vista=kardex&p=<?=$i?>"><?=$i?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>

    </div>
  </div>

  <div class="card shadow">
    <div class="card-header bg-primary text-white">Stock por Almacén</div>
    <div class="card-body p-0">
      <table class="table table-hover table-bordered mb-0">
        <thead class="table-primary">
          <tr>
            <th>Almacén</th>
            <th>Producto</th>
            <th>Stock</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($stock_data as $row):
            $badge = $row['stock'] > 0 ? 'bg-success' : 'bg-danger';
          ?>
          <tr>
            <td><?= $row['nombre_almacen'] ?></td>
            <td><?= $row['modelo'] ?></td>
            <td><span class="badge <?= $badge ?>"><?= $row['stock'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/apps.js"></script>
<script>
function autocompletarPrecio(){
    let select = document.getElementById('producto');
    let precio = select.options[select.selectedIndex].getAttribute('data-precio');
    if(precio) document.getElementById('valor').value = precio;
}
</script>

</body>
</html>