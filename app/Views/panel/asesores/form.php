<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1><?= $advisor ? 'Editar asesor' : 'Nuevo asesor' ?></h1>
    <p>El WhatsApp es el que recibe la cotización del catálogo.</p>
  </div>
  <a class="btn btn-ghost" href="<?= site_url('panel/asesores') ?>">Volver</a>
</div>

<div class="card">
  <form method="post" action="<?= $advisor ? site_url('panel/asesores/' . $advisor['id']) : site_url('panel/asesores') ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="name">Nombre</label>
        <input class="input" id="name" name="name" value="<?= esc(old('name', $advisor['name'] ?? '')) ?>" required>
      </div>
      <div class="field">
        <label for="phone">WhatsApp</label>
        <input class="input" id="phone" name="phone" value="<?= esc(old('phone', $advisor['phone'] ?? '')) ?>" placeholder="3001234567" required>
        <p class="hint">Celular colombiano a 10 dígitos, o con 57.</p>
      </div>
      <div class="field">
        <label for="slug">Enlace (opcional)</label>
        <input class="input" id="slug" name="slug" value="<?= esc(old('slug', $advisor['slug'] ?? '')) ?>" placeholder="tatiana">
      </div>
      <div class="field">
        <label for="coverage">Zona o cobertura</label>
        <input class="input" id="coverage" name="coverage" value="<?= esc(old('coverage', $advisor['coverage'] ?? '')) ?>" placeholder="Nacional · Atiende todo Colombia">
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Guardar asesor</button>
  </form>
</div>
<?= $this->endSection() ?>
