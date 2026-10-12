<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title ?? 'Vitrina') ?></title>
  <?= favicon_tags() ?>
  <?= pwa_tags() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/brand-footer.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/brand-footer.css') ?>">
</head>
<?php
  $loggedIn = ! empty($loggedIn) || (bool) session()->get('user_id');
  $panelUrl = session()->get('user_role') === 'admin' ? site_url('admin') : site_url('panel');
  $home     = site_url('/');
?>
<body data-home="<?= esc($home) ?>" data-sw="<?= esc(base_url('sw.js'), 'attr') ?>">
  <header class="mkt-header">
    <div class="wrap mkt-nav">
      <a class="brand" href="<?= $home ?>">
        <span class="brand-mark">V</span>
        Vitrina
      </a>
      <nav class="mkt-links" data-mkt-links>
        <a href="#como-funciona" data-mkt-section="como-funciona">¿Cómo funciona?</a>
        <a href="#funciones" data-mkt-section="funciones">Funciones</a>
        <a href="#negocios" data-mkt-section="negocios">Para negocios</a>
        <a href="#faq" data-mkt-section="faq">Preguntas frecuentes</a>
      </nav>
      <div class="nav-actions" data-mkt-actions>
        <?php if ($loggedIn): ?>
          <a class="btn btn-primary" href="<?= $panelUrl ?>">Ir al panel</a>
        <?php else: ?>
          <a class="btn btn-ghost" href="<?= site_url('ingresar') ?>">Ingresar</a>
          <a class="btn btn-primary" href="<?= site_url('registro') ?>">Crear mi vitrina</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <?= $this->renderSection('content') ?>
  <footer class="mkt-footer">
    <?= view('partials/brand_footer') ?>
  </footer>
  <script src="<?= base_url('assets/js/marketing.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/marketing.js') ?>"></script>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
