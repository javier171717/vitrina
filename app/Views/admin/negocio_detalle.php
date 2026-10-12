<?php
$plan      = $business['plan'] ?? 'trial';
$susp      = (int) ($business['suspended'] ?? 0) === 1;
$planText  = $plan === 'trial' ? 'En prueba' : 'Activo (pagando)';
$stateText = $susp
    ? 'Suspendido (administración — no pueden operar hasta reactivar)'
    : 'Activo';
$created = $business['created_at'] ? date('d/m/Y H:i', strtotime($business['created_at'])) : '—';
?>
<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1><?= esc($business['name']) ?> <span style="color:var(--muted);font-size:18px">#<?= (int) $business['id'] ?></span></h1>
    <p style="margin:0;line-height:1.7">
      Alta: <?= $created ?><br>
      NIT: <?= esc($business['nit'] ?: '—') ?><br>
      Plan comercial: <strong><?= $planText ?></strong>
      <?php if ($business['plan_label']): ?> · <?= esc($business['plan_label']) ?><?php endif; ?><br>
      <?php if ($plan === 'trial' && $business['trial_ends_at']): ?>
        Fin de prueba: <?= date('d/m/Y H:i', strtotime($business['trial_ends_at'])) ?><br>
      <?php endif; ?>
      <?php if ($business['next_billing_at']): ?>
        Próximo cobro: <?= date('d/m/Y H:i', strtotime($business['next_billing_at'])) ?><br>
      <?php endif; ?>
      Estado panel: <strong><?= $stateText ?></strong>
    </p>
    <?php if ($owner): ?>
      <p style="margin:6px 0 0;color:var(--muted)">
        Administrador del negocio: <?= esc($owner['name']) ?> · usuario <?= esc($owner['email']) ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<div class="detail-tabs" style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px">
  <a href="#plan" class="btn btn-ghost btn-sm">Plan y cobros</a>
  <a href="#info" class="btn btn-ghost btn-sm">Editar negocio / NIT</a>
  <a href="#password" class="btn btn-ghost btn-sm">Nueva contraseña admin</a>
  <span style="flex:1"></span>
  <a href="#danger" class="btn btn-danger btn-sm">Eliminar negocio y datos…</a>
</div>

<div class="card" id="plan" style="margin-bottom:16px">
  <h3 style="margin-top:0">Plan y cobros</h3>
  <p class="hint" style="margin-top:0">
    Definí si está en prueba, pagando o con cobro vencido.
    La opción <em>Suspender negocio</em> (más abajo) es aparte: bloquea por decisión administrativa de la plataforma.
    Con plan <strong>En prueba</strong>, el panel solo queda habilitado si la fecha de fin de prueba no pasó;
    con <strong>Activo — pagando</strong> no se exige la fecha de prueba.
  </p>
  <form method="post" action="<?= site_url('admin/negocios/' . $business['id'] . '/plan') ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="plan">Estado del plan</label>
        <select class="input" id="plan-select" name="plan">
          <option value="trial" <?= $plan === 'trial' ? 'selected' : '' ?>>En prueba (gratis)</option>
          <option value="active" <?= $plan === 'active' ? 'selected' : '' ?>>Activo — pagando</option>
        </select>
        <p class="hint">Al cobrar el mes, podés poner Activo — pagando y la fecha de próximo cobro.</p>
      </div>
      <div class="field">
        <label for="plan_label">Código o etiqueta de plan (opcional, interno)</label>
        <input class="input" id="plan_label" name="plan_label" value="<?= esc($business['plan_label'] ?? '') ?>" placeholder="ej. mensual, anual">
      </div>
      <div class="field">
        <label for="trial_ends_at">Fin de la prueba gratuita</label>
        <input class="input" type="datetime-local" id="trial_ends_at" name="trial_ends_at" value="<?= esc($business['trial_ends_at'] ? date('Y-m-d\TH:i', strtotime($business['trial_ends_at'])) : '') ?>">
        <p class="hint">Los negocios nuevos reciben 14 días de prueba al crearse; podés corregir o alargar acá.</p>
      </div>
      <div class="field">
        <label for="next_billing_at">Próximo cobro mensual (opcional)</label>
        <input class="input" type="datetime-local" id="next_billing_at" name="next_billing_at" value="<?= esc($business['next_billing_at'] ? date('Y-m-d\TH:i', strtotime($business['next_billing_at'])) : '') ?>">
        <p class="hint">Vacío si no hay mensualidad. Para borrar la fecha, dejá el campo vacío y guardá.</p>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Guardar plan y fechas</button>
  </form>
</div>

<div class="card" id="info" style="margin-bottom:16px">
  <h3 style="margin-top:0">Editar negocio / NIT</h3>
  <form method="post" action="<?= site_url('admin/negocios/' . $business['id'] . '/info') ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="biz-name">Nombre del negocio</label>
        <input class="input" id="biz-name" name="name" value="<?= esc(old('name', $business['name'])) ?>" required>
      </div>
      <div class="field">
        <label for="biz-nit">NIT / Documento</label>
        <input class="input" id="biz-nit" name="nit" value="<?= esc(old('nit', $business['nit'] ?? '')) ?>">
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Guardar</button>
  </form>
</div>

<div class="card" id="password" style="margin-bottom:16px">
  <h3 style="margin-top:0">Nueva contraseña admin</h3>
  <p class="hint" style="margin-top:0">Cambia la contraseña del dueño del negocio (<?= esc($owner['email'] ?? '') ?>).</p>
  <form method="post" action="<?= site_url('admin/negocios/' . $business['id'] . '/password') ?>">
    <?= csrf_field() ?>
    <div class="field" style="max-width:380px">
      <label for="new_password">Nueva contraseña</label>
      <input class="input" type="text" id="new_password" name="new_password" minlength="8" required>
    </div>
    <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
  </form>
</div>

<div class="card" style="margin-bottom:16px">
  <h3 style="margin-top:0"><?= $susp ? 'Reactivar negocio' : 'Suspender negocio' ?></h3>
  <p class="hint" style="margin-top:0">
    <?php if ($susp): ?>
      Este negocio está suspendido. El dueño no puede operar ni el catálogo se muestra.
    <?php else: ?>
      Suspender impide al dueño entrar al panel y oculta el catálogo público.
    <?php endif; ?>
  </p>
  <form method="post" action="<?= site_url('admin/negocios/' . $business['id'] . '/suspender') ?>"
        data-confirm
        data-confirm-title="Información del sistema"
        data-confirm-ok="<?= $susp ? 'Reactivar' : 'Suspender' ?>"
        data-confirm-danger="<?= $susp ? '0' : '1' ?>"
        data-confirm-message="<?= esc($susp
          ? 'Vas a reactivar el negocio ' . $business['name'] . '. El dueño podrá volver a entrar al panel y el catálogo público se mostrará de nuevo.'
          : 'Vas a suspender el negocio ' . $business['name'] . '. El dueño no podrá entrar al panel y el catálogo público se ocultará.', 'attr') ?>">
    <?= csrf_field() ?>
    <button class="btn <?= $susp ? 'btn-primary' : 'btn-danger' ?>" type="submit">
      <?= $susp ? 'Reactivar negocio' : 'Suspender negocio' ?>
    </button>
  </form>
</div>

<div class="card" id="danger" style="border-color:#f0c9c6;">
  <h3 style="margin-top:0;color:var(--danger)">Eliminar negocio y datos</h3>
  <p class="hint" style="margin-top:0">Se borra el negocio, sus productos, asesores, categorías y la cuenta del dueño. Esto no se puede deshacer.</p>
  <form method="post" action="<?= site_url('admin/negocios/' . $business['id'] . '/eliminar') ?>"
        data-confirm
        data-confirm-title="Información del sistema"
        data-confirm-ok="Eliminar"
        data-confirm-danger="1"
        data-confirm-message="<?= esc('Vas a eliminar el negocio ' . $business['name'] . ' y todos sus datos: productos, asesores, categorías y la cuenta del dueño. Esta acción no se puede deshacer.', 'attr') ?>">
    <?= csrf_field() ?>
    <button class="btn btn-danger" type="submit">Eliminar negocio y datos…</button>
  </form>
</div>

<div style="text-align:center;margin:28px 0">
  <a class="btn btn-ghost" href="<?= site_url('admin/negocios') ?>">← Volver al listado</a>
</div>
<?= $this->endSection() ?>
