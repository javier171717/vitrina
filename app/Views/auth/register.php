<?= $this->extend('layouts/marketing') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Crear catálogo</h1>
    <?php if (! registration_is_open()): ?>
      <p class="sub">El registro está cerrado. Si ya tienes un negocio, ingresa con tu correo. Si quieres un catálogo nuevo, pide acceso al administrador del sistema.</p>
      <?= view('partials/flash') ?>
      <a class="btn btn-primary btn-block" href="<?= site_url('ingresar') ?>">Ingresar</a>
    <?php else: ?>
    <p class="sub">Tu negocio queda listo para cargar productos y asesores.</p>
    <?= view('partials/flash') ?>
    <form method="post" action="<?= site_url('registro') ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label for="name">Tu nombre</label>
        <input class="input" id="name" name="name" value="<?= esc(old('name')) ?>" required>
      </div>
      <div class="field">
        <label for="business_name">Nombre del negocio</label>
        <input class="input" id="business_name" name="business_name" value="<?= esc(old('business_name')) ?>" required>
      </div>
      <div class="field">
        <label for="email">Correo</label>
        <input class="input" type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required>
      </div>
      <div class="field">
        <label for="password">Contraseña</label>
        <input class="input" type="password" id="password" name="password" minlength="8" required>
        <p class="hint">Mínimo 8 caracteres.</p>
      </div>
      <button class="btn btn-primary btn-block" type="submit">Crear mi catálogo</button>
    </form>
    <p class="hint" style="margin-top:16px">¿Ya tienes cuenta? <a href="<?= site_url('ingresar') ?>">Ingresa</a>.</p>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>
