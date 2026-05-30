<?php
if (estaLogueado()) {
    $vistaDefault = rolActual() === 'admin' ? 'productos' : 'ventas';
    header("Location: /index.php?vista=$vistaDefault");
    exit;
}

$msg = $_GET['msg'] ?? '';
$mensajes = [
    'credenciales'   => ['tipo' => 'danger',  'texto' => 'Email o contraseña incorrectos.'],
    'campos_vacios'  => ['tipo' => 'warning', 'texto' => 'Por favor completa todos los campos.'],
    'sesion_cerrada' => ['tipo' => 'success', 'texto' => 'Sesión cerrada correctamente.'],
    'sin_permiso'    => ['tipo' => 'warning', 'texto' => 'No tienes permisos para acceder a esa sección.'],
];
$alerta = $mensajes[$msg] ?? null;
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="card shadow" style="width: 100%; max-width: 400px;">

    <div class="card-header bg-primary text-white text-center py-4">
      <h4 class="mb-0 fw-bold">Sistema de Gestión</h4>
      <small class="opacity-75">Ingresa tus credenciales</small>
    </div>

    <div class="card-body p-4">

      <?php if ($alerta): ?>
        <div class="alert alert-<?= $alerta['tipo'] ?> py-2 small">
          <?= $alerta['texto'] ?>
        </div>
      <?php endif; ?>

      <form action="/php/login.php" method="POST">

        <div class="mb-3">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control"
                 placeholder="tu@email.com" required autofocus>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold">Contraseña</label>
          <input type="password" name="password" class="form-control"
                 placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
          Ingresar
        </button>

      </form>

    </div>

  </div>
</div>