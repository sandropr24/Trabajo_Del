<?php
$action = 'listar';
$data = include(__DIR__ . '/../php/productos.php');
$productos_activos   = $data['activos'];
$productos_inactivos = $data['inactivos'];
?>

<div class="container mt-5">
  <div id="alerta" class="alert d-none mt-3"></div>

  <div class="mb-4">
    <h1 class="text-primary mb-3">Productos</h1>
    <div class="d-flex justify-content-end gap-2">
      <a href="index.php?vista=kardex" class="btn btn-primary">Ver Kardex</a>
      <a href="index.php?vista=ventas" class="btn btn-primary">Ver Ventas</a>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Registrar Producto</div>
    <div class="card-body">
      <form action="/php/productos.php?action=insertar" method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-5">
          <label class="form-label">Modelo</label>
          <input type="text" name="modelo" class="form-control" placeholder="Ingrese modelo" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Precio</label>
          <input type="number" name="precio" class="form-control" placeholder="Ingrese precio" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Imagen</label>
          <input type="file" name="imagen" class="form-control" accept="image/*">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="/php/productos.php?action=actualizar" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
              <label class="form-label">Modelo</label>
              <input type="text" name="modelo" id="edit_modelo" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Precio</label>
              <input type="number" name="precio" id="edit_precio" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Actualizar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#activos">Activos</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inactivos">Inactivos</a></li>
      </ul>
    </div>
    <div class="card-body tab-content">

      <div class="tab-pane fade show active" id="activos">
        <table class="table table-bordered">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Imagen</th>
              <th>Modelo</th>
              <th>Precio</th>
              <th>Creado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($productos_activos as $row): ?>
              <tr>
                <td><?= $row['id_producto'] ?></td>
                <td>
                  <?php if($row['imagen']): ?>
                    <img src="/img/<?= $row['imagen'] ?>" style="height:40px; object-fit:contain;">
                  <?php else: ?>
                    <span class="text-muted">Sin imagen</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['modelo']) ?></td>
                <td>S/. <?= $row['precio_actual'] ?></td>
                <td><?= date('Y-m-d', strtotime($row['create_at'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-warning"
                    onclick='editar(<?= $row['id_producto'] ?>, <?= json_encode($row['modelo']) ?>, <?= $row['precio_actual'] ?>)'>
                    Editar
                  </button>
                  <a href="/php/productos.php?action=desactivar&id=<?= $row['id_producto'] ?>"
                     class="btn btn-sm btn-danger">Desactivar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="tab-pane fade" id="inactivos">
        <table class="table table-bordered">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Imagen</th>
              <th>Modelo</th>
              <th>Precio</th>
              <th>Creado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($productos_inactivos as $row): ?>
              <tr>
                <td><?= $row['id_producto'] ?></td>
                <td>
                  <?php if($row['imagen']): ?>
                    <img src="/img/<?= $row['imagen'] ?>" style="height:40px; object-fit:contain;">
                  <?php else: ?>
                    <span class="text-muted">Sin imagen</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['modelo']) ?></td>
                <td>S/. <?= $row['precio_actual'] ?></td>
                <td><?= date('Y-m-d', strtotime($row['create_at'])) ?></td>
                <td>
                  <a href="/php/productos.php?action=reactivar&id=<?= $row['id_producto'] ?>"
                     class="btn btn-sm btn-success">Reactivar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<script src="js/productos.js"></script>