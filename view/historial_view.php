<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
$action = 'listar';
$ventas = include("php/historial.php");
?>

<div class="container mt-5">
  <div id="alerta" class="alert d-none mt-3"></div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-primary">Historial de Ventas</h1>
    <div>
      <a href="index.php?vista=ventas" class="btn btn-primary btn-sm">Ver Ventas</a>
      <a href="index.php?vista=productos" class="btn btn-primary btn-sm">Ver Productos</a>
    </div>
  </div>

  <div class="modal fade" id="modalDetalle">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Detalle de Venta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenido-detalle">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-header bg-primary text-white">Ventas Registradas</div>
    <div class="card-body p-0">
      <table class="table table-hover table-bordered mb-0">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Título</th>
            <th>Total</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($ventas as $row): ?>
          <tr>
            <td><?=$row['id_venta']?></td>
            <td><?=$row['fecha']?></td>
            <td><?=$row['titulo']?></td>
            <td>S/. <?=number_format($row['total'], 2)?></td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="verDetalle(<?=$row['id_venta']?>)">Ver</button>
            </td>
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
async function verDetalle(id_venta){
    var res = await fetch('php/historial.php?action=detalle&id_venta=' + id_venta);
    var data = await res.json();

    var html = '<table class="table table-bordered"><thead class="table-primary"><tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr></thead><tbody>';
    data.forEach(item => {
        html += '<tr><td>' + item.modelo + '</td><td>' + item.cantidad + '</td><td>S/. ' + parseFloat(item.precio_unitario).toFixed(2) + '</td><td>S/. ' + parseFloat(item.subtotal).toFixed(2) + '</td></tr>';
    });
    html += '</tbody></table>';

    document.getElementById('contenido-detalle').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
}
</script>
</body>
</html>