<?php
$brandColor = $brandColor ?? ($business['primary_color'] ?? '#e30613');
if (! is_string($brandColor) || ! preg_match('/^#[0-9A-Fa-f]{6}$/', $brandColor)) {
    $brandColor = '#e30613';
}
$onDark = ! empty($onDark);
$waUrl  = 'https://wa.me/' . whatsapp_digits('3215821483');
?>
<div class="brand-credit<?= $onDark ? ' brand-credit--dark' : '' ?>" style="--brand-accent: <?= esc($brandColor, 'attr') ?>">
  <p class="brand-credit-line">
    Desarrollado con <span class="brand-heart" aria-hidden="true">❤</span> por
    <span class="brand-code">&lt;/&gt;</span>
    <strong class="brand-name">SanabriaCod</strong>
  </p>
  <p class="brand-credit-tag">Pasión por lo que hacemos</p>
  <a class="brand-wa" href="<?= esc($waUrl, 'attr') ?>" target="_blank" rel="noopener" aria-label="WhatsApp 321 582 1483">
    <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
      <path fill="#25D366" d="M12.04 2C6.58 2 2.15 6.43 2.15 11.89c0 1.95.57 3.84 1.65 5.49L2 22l4.76-1.75a9.86 9.86 0 0 0 5.28 1.52h.01c5.46 0 9.89-4.43 9.89-9.89C21.94 6.43 17.5 2 12.04 2z"/>
      <path fill="#fff" d="M17.76 16.01c-.24.67-1.4 1.24-1.94 1.32-.5.07-1.13.1-1.83-.11-.42-.13-.96-.31-1.66-.61-2.92-1.26-4.82-4.2-4.97-4.39-.14-.2-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36h.56c.18 0 .42-.07.66.5.24.58.82 2 .89 2.15.07.14.12.31.02.5-.1.2-.14.31-.28.48-.14.17-.3.37-.42.5-.14.14-.29.29-.12.56.16.28.73 1.2 1.56 1.94 1.08.96 1.98 1.26 2.26 1.4.28.14.44.12.6-.07.16-.2.7-.81.88-1.09.19-.28.37-.23.62-.14.26.1 1.64.77 1.92.91.28.14.47.21.54.33.07.12.07.67-.17 1.34z"/>
    </svg>
    <span class="brand-wa-tip">WhatsApp 321 582 1483</span>
  </a>
</div>
