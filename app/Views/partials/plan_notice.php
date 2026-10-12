<?php
$notice = business_plan_notice($business ?? null);
if (! $notice) {
    return;
}

$isExpired = $notice['type'] === 'expired';

// El aviso de vencimiento próximo solo se muestra una vez por inicio de sesión.
if (! $isExpired) {
    if (session()->get('plan_notice_shown')) {
        return;
    }
    session()->set('plan_notice_shown', true);
}
?>
<div class="plan-overlay" id="plan-overlay">
  <div class="plan-modal <?= $isExpired ? 'plan-modal-expired' : '' ?>">
    <div class="plan-modal-icon">
      <span class="material-symbols-outlined" style="font-size:28px;color:<?= $isExpired ? 'var(--danger)' : 'var(--primary)' ?>">info</span>
    </div>
    <h3 class="plan-modal-title"><?= esc($notice['title']) ?></h3>
    <div class="plan-modal-body">
      <p><?= $notice['message'] ?></p>
      <p style="margin-top:14px;font-weight:700">¡Gracias por su atención!</p>
    </div>
    <?php if (! $isExpired): ?>
      <button class="btn btn-primary" type="button" id="plan-notice-ok">OK</button>
    <?php endif; ?>
  </div>
</div>
<?php if (! $isExpired): ?>
<script>
document.getElementById('plan-notice-ok')?.addEventListener('click', () => {
  document.getElementById('plan-overlay').remove();
});
</script>
<?php endif; ?>
