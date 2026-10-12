<?= $this->extend('layouts/marketing') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Ingresar</h1>
    <p class="sub">Entra al panel de tu negocio.</p>
    <?= view('partials/flash') ?>
    <form method="post" action="<?= site_url('ingresar') ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label for="email">Correo</label>
        <input class="input" type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required>
      </div>
      <div class="field">
        <label for="password">Contraseña</label>
        <input class="input" type="password" id="password" name="password" required>
      </div>
      <button class="btn btn-primary btn-block" type="submit">Entrar</button>
    </form>
    <p class="hint" style="margin-top:16px">¿Aún no tienes catálogo? <a href="<?= site_url('registro') ?>">Créalo aquí</a>.</p>
  </div>
</div>
<?= $this->endSection() ?>
