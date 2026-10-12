<?php
$primary = $business['primary_color'] ?: '#e30613';
$logo    = ! empty($business['logo']) ? upload_url($business['logo']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Cotización · <?= esc($business['name']) ?></title>
  <meta name="description" content="Cotización de <?= esc($business['name']) ?>.">
  <?= favicon_tags($business) ?>
  <?= pwa_tags($business, $primary) ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/quote.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/quote.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/brand-footer.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/brand-footer.css') ?>">
  <style>:root { --primary-color: <?= esc($primary) ?>; }</style>
</head>
<body data-sw="<?= esc(base_url('sw.js'), 'attr') ?>">
  <main class="quote-page">
    <article class="ticket<?= $hasPrices ? ' has-prices' : '' ?>">
      <header class="ticket-brand">
        <?php if ($logo): ?>
          <img class="ticket-logo" src="<?= esc($logo) ?>" alt="<?= esc($business['name']) ?>">
        <?php else: ?>
          <strong class="ticket-shop"><?= esc($business['name']) ?></strong>
        <?php endif; ?>
        <?php if (! empty($business['city'])): ?>
          <p class="ticket-city"><?= esc($business['city']) ?></p>
        <?php endif; ?>
      </header>

      <p class="ticket-kicker">Cotización</p>
      <hr class="dash">
      <h1>COTIZACIÓN · <?= esc($when) ?></h1>

      <dl class="meta">
        <div>
          <dt>Fecha</dt>
          <dd><?= esc($when) ?></dd>
        </div>
        <div>
          <dt>Canal</dt>
          <dd><?= $wholesale ? 'Al por mayor' : 'Al detalle' ?></dd>
        </div>
      </dl>

      <div class="cols">
        <span>Producto</span>
        <span>Cant.</span>
        <span><?= $hasPrices ? 'Total' : '' ?></span>
      </div>
      <hr class="line">

      <?php foreach ($items as $item): ?>
        <section class="item">
          <div class="item-top">
            <div class="item-name">
              <?= esc($item['name']) ?>
              <?php if (! empty($item['wholesale'])): ?>
                <span class="badge-mayor">al por mayor</span>
              <?php endif; ?>
            </div>
            <div class="item-qty"><?= (int) $item['qty'] ?> un</div>
            <?php if ($hasPrices): ?>
              <div class="item-total"><?= esc(money_cop($item['subtotal'])) ?></div>
            <?php endif; ?>
          </div>
          <?php if ($hasPrices): ?>
            <p class="item-unit"><?= esc(money_cop($item['unit_price'])) ?> c/u</p>
          <?php endif; ?>
          <?php if (! empty($item['color'])): ?>
            <p class="item-sku">Color: <?= esc($item['color']) ?></p>
          <?php endif; ?>
          <?php if (! empty($item['size'])): ?>
            <p class="item-sku">Talla: <?= esc($item['size']) ?></p>
          <?php endif; ?>
          <?php if ($item['sku'] !== ''): ?>
            <p class="item-sku">Ref. <?= esc($item['sku']) ?></p>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>

      <?php if ($hasPrices): ?>
        <hr class="thick">
        <p class="total-label">TOTAL COTIZADO</p>
        <p class="total-value"><?= esc(money_cop($total)) ?></p>
      <?php endif; ?>
      <hr class="dash">
      <p class="ticket-note">Precios sujetos a disponibilidad y confirmación del asesor.</p>
    </article>
  </main>
  <?= view('partials/brand_footer', ['brandColor' => $primary, 'onDark' => false]) ?>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
