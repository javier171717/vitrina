<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Control del sistema</h1>
    <p>Tú decides si los clientes pueden crear su catálogo y cuáles quedan publicados.</p>
  </div>
</div>

<div class="grid-stats">
  <div class="stat"><b><?= (int) $ownerCount ?></b><span>Dueños</span></div>
  <div class="stat"><b><?= (int) $businessCount ?></b><span>Negocios</span></div>
  <div class="stat"><b><?= (int) $activeCatalogs ?></b><span>Catálogos publicados</span></div>
</div>

<div class="card" style="margin-bottom:16px">
  <h3 style="margin-top:0">Registro de nuevos catálogos</h3>
  <p class="hint" style="margin-top:0">Si está apagado, nadie puede entrar a “Crear mi catálogo”. Los negocios que ya existen siguen funcionando.</p>
  <form method="post" action="<?= site_url('admin/registro') ?>">
    <?= csrf_field() ?>
    <label class="check" style="margin:16px 0">
      <input type="checkbox" name="registration_open" value="1" <?= ! empty($registrationOpen) ? 'checked' : '' ?>>
      Permitir que los clientes creen su portafolio
    </label>
    <div>
      <button class="btn btn-primary" type="submit">Guardar</button>
    </div>
  </form>
</div>
<?= $this->endSection() ?>
