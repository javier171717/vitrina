<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Panel') ?> · <?= esc($business['name'] ?? 'Vitrina') ?></title>
  <?= favicon_tags($business ?? null) ?>
  <?= pwa_tags($business ?? null) ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/brand-footer.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/brand-footer.css') ?>">
</head>
<body data-sw="<?= esc(base_url('sw.js'), 'attr') ?>">
  <div class="backdrop"></div>
  <div class="panel">
    <aside class="sidebar">
      <a class="brand" href="<?= site_url('panel') ?>">
        <span class="brand-mark">V</span>
        <?= esc($business['name']) ?>
      </a>
      <nav class="side-nav">
        <a class="<?= ($section ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('panel') ?>">Resumen</a>
        <a class="<?= ($section ?? '') === 'negocio' ? 'active' : '' ?>" href="<?= site_url('panel/negocio') ?>">Mi negocio</a>
        <a class="<?= ($section ?? '') === 'asesores' ? 'active' : '' ?>" href="<?= site_url('panel/asesores') ?>">Asesores</a>
        <a class="<?= ($section ?? '') === 'categorias' ? 'active' : '' ?>" href="<?= site_url('panel/categorias') ?>">Categorías</a>
        <a class="<?= ($section ?? '') === 'productos' ? 'active' : '' ?>" href="<?= site_url('panel/productos') ?>">Productos</a>
      </nav>
      <div class="side-foot">
        <div class="who"><?= esc($user['name']) ?></div>
        <a class="btn btn-ghost btn-sm" href="<?= site_url('salir') ?>" style="color:#fff;border-color:#333">Salir</a>
      </div>
    </aside>
    <main class="main">
      <div class="main-body">
      <button class="btn btn-ghost menu-btn" type="button" data-menu>Menú</button>
      <?= view('partials/flash') ?>
      <?= $this->renderSection('content') ?>
      </div>
      <?= view('partials/brand_footer', ['brandColor' => $business['primary_color'] ?? '#e30613']) ?>
    </main>
  </div>
  <?= view('partials/plan_notice', ['business' => $business ?? null]) ?>
  <script src="<?= base_url('assets/js/panel.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/panel.js') ?>"></script>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
