<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Tu cuenta</h1>
    <p>Cambia el nombre, el correo de acceso o la contraseña del super admin.</p>
  </div>
</div>

<div class="card">
  <form method="post" action="<?= site_url('admin/cuenta') ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label for="name">Nombre</label>
      <input class="input" id="name" name="name" value="<?= esc(old('name', $user['name'])) ?>" required>
    </div>
    <div class="field">
      <label for="email">Correo de acceso</label>
      <input class="input" type="email" id="email" name="email" value="<?= esc(old('email', $user['email'])) ?>" required autocomplete="username">
      <p class="hint">Con este correo entras al panel de super admin.</p>
    </div>
    <div class="field">
      <label for="current_password">Contraseña actual</label>
      <div class="password-wrap">
        <input class="input" type="password" id="current_password" name="current_password" autocomplete="current-password">
        <button class="password-toggle" type="button" data-toggle-password="current_password" aria-label="Mostrar contraseña">
          <span class="material-symbols-outlined">visibility</span>
        </button>
      </div>
      <p class="hint">Obligatoria si cambias el correo o la contraseña.</p>
    </div>
    <div class="field">
      <label for="new_password">Nueva contraseña</label>
      <div class="password-wrap">
        <input class="input" type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password">
        <button class="password-toggle" type="button" data-toggle-password="new_password" aria-label="Mostrar contraseña">
          <span class="material-symbols-outlined">visibility</span>
        </button>
      </div>
      <p class="hint">Déjala vacía si no quieres cambiarla. Mínimo 8 caracteres.</p>
    </div>
    <button class="btn btn-primary" type="submit">Guardar</button>
  </form>
</div>
<?= $this->endSection() ?>
