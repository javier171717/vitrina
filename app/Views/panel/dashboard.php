<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Resumen</h1>
    <p>Arma tu catálogo y comparte el enlace con tus clientes.</p>
  </div>
  <a class="btn btn-primary" href="<?= catalog_url($business['slug']) ?>" target="_blank" rel="noopener">Ver catálogo</a>
</div>

<div class="grid-stats">
  <div class="stat"><b><?= (int) $advisors ?></b><span>Asesores</span></div>
  <div class="stat"><b><?= (int) $categories ?></b><span>Categorías</span></div>
  <div class="stat"><b><?= (int) $products ?></b><span>Productos</span></div>
</div>

<div class="card" style="margin-bottom:16px">
  <h3 style="margin-top:0">Pasos para publicar</h3>
  <div class="steps">
    <div class="step <?= $business['logo'] || $business['phone'] ? 'done' : '' ?>">
      <div>1. Completa nombre, logo y contacto</div>
      <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/negocio') ?>">Editar</a>
    </div>
    <div class="step <?= $advisors ? 'done' : '' ?>">
      <div>2. Agrega al menos un asesor con WhatsApp</div>
      <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/asesores') ?>">Asesores</a>
    </div>
    <div class="step <?= $categories ? 'done' : '' ?>">
      <div>3. Crea categorías de tu portafolio</div>
      <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/categorias') ?>">Categorías</a>
    </div>
    <div class="step <?= $products ? 'done' : '' ?>">
      <div>4. Sube productos con foto y referencia</div>
      <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/productos') ?>">Productos</a>
    </div>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0">Enlace público</h3>
  <p class="hint">Si tienes un asesor, el cliente entra directo a cotizar con él. Si hay varios, primero elige.</p>
  <div class="share-box">
    <input class="input" id="public-link" readonly value="<?= catalog_url($business['slug']) ?>">
    <button class="btn btn-dark btn-sm" type="button" data-copy="#public-link">Copiar</button>
  </div>
  <?php if (! empty($advisorList)): ?>
    <div class="table-wrap" style="margin-top:16px">
      <table>
        <thead><tr><th>Asesor</th><th>Enlace</th></tr></thead>
        <tbody>
        <?php foreach ($advisorList as $advisor): ?>
          <tr>
            <td><?= esc($advisor['name']) ?></td>
            <td><a href="<?= catalog_url($business['slug'], $advisor['slug']) ?>" target="_blank"><?= catalog_url($business['slug'], $advisor['slug']) ?></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>
