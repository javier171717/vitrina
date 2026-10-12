<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? $business['name']) ?></title>
  <?= favicon_tags($business) ?>
  <?= pwa_tags($business) ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
  <link rel="stylesheet" href="<?= base_url('assets/css/catalog.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/catalog.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/brand-footer.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/brand-footer.css') ?>">
  <style>:root { --primary-color: <?= esc($business['primary_color'] ?: '#e30613') ?>; }</style>
</head>
<body data-sw="<?= esc(base_url('sw.js'), 'attr') ?>"<?= catalog_owner_return_url($business) ? ' class="has-owner-bar"' : '' ?>>
  <header class="cat-header">
    <?= view('partials/catalog_owner_bar', ['business' => $business]) ?>
    <div class="cat-header-inner">
      <a class="cat-brand" href="<?= catalog_url($business['slug']) ?>">
        <?php if (! empty($business['logo'])): ?>
          <img class="cat-logo" src="<?= upload_url($business['logo']) ?>" alt="<?= esc($business['name']) ?>">
        <?php else: ?>
          <span class="cat-logo-fallback"><?= esc($business['name']) ?></span>
        <?php endif; ?>
      </a>
    </div>
  </header>

  <main class="pick">
    <p class="kicker" style="color:var(--primary-color);font-family:var(--display);letter-spacing:.14em;text-transform:uppercase;font-weight:700">Catálogo <?= esc($business['name']) ?></p>
    <h1>¿Con quién quieres cotizar?</h1>
    <p class="muted">Elige un asesor. La lista se envía a su WhatsApp.</p>

    <?php if (! $advisors): ?>
      <div class="empty-cat">Este negocio todavía no tiene asesores publicados.</div>
    <?php else: ?>
      <div class="advisor-grid">
        <?php foreach ($advisors as $advisor): ?>
          <a class="advisor-card" href="<?= catalog_url($business['slug'], $advisor['slug']) ?>">
            <span class="material-symbols-outlined" style="color:var(--primary-color)">support_agent</span>
            <h3><?= esc($advisor['name']) ?></h3>
            <?php if ($advisor['coverage']): ?><p><?= esc($advisor['coverage']) ?></p><?php endif; ?>
            <span class="quote-btn">Cotizar con <?= esc($advisor['name']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
  <footer class="cat-footer">
    <?= view('partials/brand_footer', ['brandColor' => $business['primary_color'] ?: '#e30613', 'onDark' => true]) ?>
  </footer>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
