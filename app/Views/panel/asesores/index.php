<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Asesores</h1>
    <p>Cada asesor tiene su enlace. El cliente cotiza con esa persona por WhatsApp.</p>
  </div>
  <a class="btn btn-primary" href="<?= site_url('panel/asesores/nuevo') ?>">Nuevo asesor</a>
</div>

<div class="card">
  <?php if (! $advisors): ?>
    <div class="empty">Aún no hay asesores. Crea el primero para poder compartir el catálogo.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>WhatsApp</th>
            <th>Cobertura</th>
            <th>Enlace</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($advisors as $advisor): ?>
          <tr>
            <td><?= esc($advisor['name']) ?></td>
            <td><?= esc($advisor['phone']) ?></td>
            <td><?= esc($advisor['coverage']) ?></td>
            <td><a href="<?= catalog_url($business['slug'], $advisor['slug']) ?>" target="_blank">/<?= esc($advisor['slug']) ?></a></td>
            <td class="row-actions">
              <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/asesores/' . $advisor['id'] . '/editar') ?>">Editar</a>
              <form method="post" action="<?= site_url('panel/asesores/' . $advisor['id'] . '/eliminar') ?>" onsubmit="return confirm('¿Eliminar este asesor?')">
                <?= csrf_field() ?>
                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>
