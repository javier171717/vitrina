<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Negocios</h1>
    <p>Administra los catálogos de tus clientes.</p>
  </div>
  <a class="btn btn-primary" href="<?= site_url('admin/negocios/nuevo') ?>">Alta de negocio y dueño</a>
</div>

<div class="card">
  <?php if (! $businesses): ?>
    <div class="empty">Todavía no hay negocios registrados.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Plan</th>
            <th>Estado panel</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($businesses as $row):
          $plan     = $row['plan'] ?? 'trial';
          $susp     = (int) ($row['suspended'] ?? 0) === 1;
          $planText = $plan === 'trial' ? 'En prueba' : 'Activo (pagando)';
          if ($row['plan_label']) $planText .= ' · ' . esc($row['plan_label']);
          $stateOk  = ! $susp && (int) ($row['user_is_active'] ?? 1) === 1;
        ?>
          <tr>
            <td>
              <a href="<?= site_url('admin/negocios/' . $row['id']) ?>" style="text-decoration:none;color:var(--primary);font-weight:600">
                <?= esc($row['name']) ?>
              </a>
            </td>
            <td><?= $planText ?></td>
            <td>
              <?php if ($susp): ?>
                <span class="badge badge-off">Suspendido</span>
              <?php else: ?>
                <span class="badge badge-ok">Activo</span>
              <?php endif; ?>
            </td>
            <td style="white-space:nowrap">
              <form method="post" action="<?= site_url('admin/negocios/' . $row['id'] . '/suspender') ?>" style="display:inline"
                    data-confirm
                    data-confirm-title="Información del sistema"
                    data-confirm-ok="<?= $susp ? 'Reactivar' : 'Suspender' ?>"
                    data-confirm-danger="<?= $susp ? '0' : '1' ?>"
                    data-confirm-message="<?= esc($susp
                      ? 'Vas a reactivar el negocio ' . $row['name'] . '. El dueño podrá volver a entrar al panel y el catálogo público se mostrará de nuevo.'
                      : 'Vas a suspender el negocio ' . $row['name'] . '. El dueño no podrá entrar al panel y el catálogo público se ocultará.', 'attr') ?>">
                <?= csrf_field() ?>
                <button class="btn <?= $susp ? 'btn-ghost' : 'btn-danger' ?> btn-sm" type="submit">
                  <?= $susp ? 'Reactivar' : 'Suspender' ?>
                </button>
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
