<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Control') ?> · Super admin</title>
  <?= favicon_tags() ?>
  <?= pwa_tags() ?>
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
      <a class="brand" href="<?= site_url('admin') ?>">
        <span class="brand-mark">V</span>
        Super admin
      </a>
      <nav class="side-nav">
        <a class="<?= ($section ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('admin') ?>">Control</a>
        <a class="<?= ($section ?? '') === 'negocios' ? 'active' : '' ?>" href="<?= site_url('admin/negocios') ?>">Negocios</a>
        <a class="<?= ($section ?? '') === 'cuenta' ? 'active' : '' ?>" href="<?= site_url('admin/cuenta') ?>">Tu cuenta</a>
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
      <?= view('partials/brand_footer') ?>
    </main>
  </div>
  <script src="<?= base_url('assets/js/panel.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/panel.js') ?>"></script>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
