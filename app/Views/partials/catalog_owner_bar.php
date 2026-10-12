<?php
$panelUrl = catalog_owner_return_url($business ?? null);
if (! $panelUrl) {
    return;
}
$isAdmin = session()->get('user_role') === 'admin';
?>
<div class="owner-bar">
  <span><?= $isAdmin ? 'Vista pública del catálogo' : 'Así ven tu catálogo los clientes' ?></span>
  <a href="<?= esc($panelUrl) ?>"><?= $isAdmin ? 'Volver al admin' : 'Volver al panel' ?></a>
</div>
