<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Alta de negocio y dueño</h1>
    <p>Crea la cuenta del dueño y su negocio de una vez.</p>
  </div>
  <a class="btn btn-ghost" href="<?= site_url('admin/negocios') ?>">← Volver al listado</a>
</div>

<div class="card">
  <form method="post" action="<?= site_url('admin/negocios/nuevo') ?>">
    <?= csrf_field() ?>
    <h3 style="margin-top:0">Datos del dueño</h3>
    <div class="form-grid">
      <div class="field">
        <label for="owner_name">Nombre del dueño</label>
        <input class="input" id="owner_name" name="owner_name" value="<?= esc(old('owner_name')) ?>" required>
      </div>
      <div class="field">
        <label for="owner_email">Correo</label>
        <input class="input" type="email" id="owner_email" name="owner_email" value="<?= esc(old('owner_email')) ?>" required>
      </div>
      <div class="field">
        <label for="owner_password">Contraseña</label>
        <input class="input" type="text" id="owner_password" name="owner_password" value="<?= esc(old('owner_password')) ?>" minlength="8" required>
        <p class="hint">Mínimo 8 caracteres. Se la das al dueño para que entre.</p>
      </div>
    </div>

    <h3>Datos del negocio</h3>
    <div class="form-grid">
      <div class="field">
        <label for="business_name">Nombre del negocio</label>
        <input class="input" id="business_name" name="business_name" value="<?= esc(old('business_name')) ?>" required>
      </div>
      <div class="field">
        <label for="nit">NIT / Documento (opcional)</label>
        <input class="input" id="nit" name="nit" value="<?= esc(old('nit')) ?>">
      </div>
    </div>

    <h3>Plan</h3>
    <div class="form-grid">
      <div class="field">
        <label for="plan">Estado del plan</label>
        <select class="input" id="plan" name="plan">
          <option value="trial" <?= old('plan', 'trial') === 'trial' ? 'selected' : '' ?>>En prueba (gratis)</option>
          <option value="active" <?= old('plan') === 'active' ? 'selected' : '' ?>>Activo — pagando</option>
        </select>
      </div>
      <div class="field">
        <label for="plan_label">Código o etiqueta de plan (opcional, interno)</label>
        <input class="input" id="plan_label" name="plan_label" value="<?= esc(old('plan_label')) ?>" placeholder="ej. mensual, anual">
      </div>
      <div class="field">
        <label for="trial_days">Días de prueba</label>
        <input class="input" type="number" id="trial_days" name="trial_days" value="<?= esc(old('trial_days', 14)) ?>" min="1">
        <p class="hint">Solo aplica si el plan es «En prueba».</p>
      </div>
      <div class="field">
        <label for="next_billing_at">Próximo cobro (opcional)</label>
        <input class="input" type="datetime-local" id="next_billing_at" name="next_billing_at" value="<?= esc(old('next_billing_at')) ?>">
        <p class="hint">Solo aplica si el plan es «Activo — pagando».</p>
      </div>
    </div>

    <button class="btn btn-primary" type="submit">Crear negocio y dueño</button>
  </form>
</div>
<?= $this->endSection() ?>
