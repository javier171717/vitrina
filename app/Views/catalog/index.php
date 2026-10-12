<?php
$primary = $business['primary_color'] ?: '#e30613';
$bannerDesktop = $business['banner'] ? upload_url($business['banner']) : '';
$bannerMobile  = $business['banner_mobile'] ? upload_url($business['banner_mobile']) : $bannerDesktop;
$payloadAdvisor = [
    'name'  => $advisor['name'],
    'slug'  => $advisor['slug'],
    'phone' => whatsapp_digits($advisor['phone']),
];
$payloadBusiness = [
    'name'                => $business['name'],
    'slug'                => $business['slug'],
    'wholesale_min_total' => $business['wholesale_min_total'] !== null && $business['wholesale_min_total'] !== ''
        ? (float) $business['wholesale_min_total']
        : null,
];
$canChangeAdvisor = isset($advisors) && count($advisors) > 1;
$pickUrl = catalog_url($business['slug']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= esc($title) ?></title>
  <meta name="description" content="Catálogo <?= esc($business['name']) ?>. Arma tu cotización y envíasela a <?= esc($advisor['name']) ?> por WhatsApp.">
  <?= favicon_tags($business) ?>
  <?= pwa_tags($business, $primary) ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add,add_shopping_cart,call,check_circle,close,delete,inventory_2,location_on,mail,search,shopping_cart,support_agent">
  <link rel="stylesheet" href="<?= base_url('assets/css/catalog.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/catalog.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/brand-footer.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/brand-footer.css') ?>">
  <style>:root { --primary-color: <?= esc($primary) ?>; }</style>
</head>
<body
  id="catalog-app"
  data-sw="<?= esc(base_url('sw.js'), 'attr') ?>"
  data-show-prices="<?= ! empty($business['show_prices']) ? '1' : '0' ?>"
  data-quote-url="<?= esc(site_url('c/' . $business['slug'] . '/' . $advisor['slug'] . '/cotizacion')) ?>"
  data-csrf-name="<?= esc(csrf_token()) ?>"
  data-csrf-hash="<?= esc(csrf_hash()) ?>"
  data-csrf-header="<?= esc(csrf_header()) ?>"
  <?= catalog_owner_return_url($business) ? 'class="has-owner-bar"' : '' ?>
>
<script id="catalog-products" type="application/json"><?= json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script id="catalog-business" type="application/json"><?= json_encode($payloadBusiness, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script id="catalog-advisor" type="application/json"><?= json_encode($payloadAdvisor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
  <header class="cat-header">
    <?= view('partials/catalog_owner_bar', ['business' => $business]) ?>
    <div class="cat-header-inner">
      <div class="cat-brand">
        <a href="<?= catalog_url($business['slug'], $advisor['slug']) ?>">
          <?php if (! empty($business['logo'])): ?>
            <img class="cat-logo" src="<?= upload_url($business['logo']) ?>" alt="<?= esc($business['name']) ?>">
          <?php else: ?>
            <span class="cat-logo-fallback"><?= esc($business['name']) ?></span>
          <?php endif; ?>
        </a>
        <span class="advisor-chip">
          <span class="material-symbols-outlined" style="color:var(--primary-color);font-size:18px">support_agent</span>
          Cotizando con <strong><?= esc($advisor['name']) ?></strong>
          <?php if ($canChangeAdvisor): ?>
            <a class="advisor-change" href="<?= esc($pickUrl) ?>">Cambiar</a>
          <?php endif; ?>
        </span>
      </div>
      <button class="quote-btn" type="button" data-open-cart>
        <span class="material-symbols-outlined icon-fill">shopping_cart</span>
        <span class="label">Enviar cotización</span>
        <span class="quote-btn-total" id="quote-btn-total" hidden></span>
        <span class="cart-count" id="cart-count">0</span>
      </button>
    </div>
    <div class="mobile-advisor">
      <span class="material-symbols-outlined" style="color:var(--primary-color);font-size:18px">support_agent</span>
      Cotizando con <strong><?= esc($advisor['name']) ?></strong>
      <?php if ($canChangeAdvisor): ?>
        <a class="advisor-change" href="<?= esc($pickUrl) ?>">Cambiar</a>
      <?php endif; ?>
    </div>
  </header>

  <?php if ($bannerDesktop): ?>
    <section class="banner">
      <picture>
        <source media="(min-width: 768px)" srcset="<?= esc($bannerDesktop) ?>">
        <img src="<?= esc($bannerMobile) ?>" alt="<?= esc($business['name']) ?>">
      </picture>
    </section>
  <?php endif; ?>

  <div class="search-bar">
    <div class="search-inner">
      <div class="search-box">
        <span class="material-symbols-outlined icon">search</span>
        <input id="catalog-search" type="search" placeholder="Buscar producto, referencia o moto…" aria-label="Buscar en el catálogo">
      </div>
      <span id="catalog-status" class="sr-only" style="position:absolute;left:-9999px"></span>
    </div>
    <div class="chips">
      <button class="chip active" type="button" data-cat-id="0">Todas</button>
      <?php foreach ($categories as $category): ?>
        <button class="chip" type="button" data-cat-id="<?= (int) $category['id'] ?>"><?= esc($category['name']) ?></button>
      <?php endforeach; ?>
    </div>
  </div>

  <main class="layout">
    <aside class="cats">
      <button type="button" class="active" data-cat-id="0">Todas</button>
      <?php foreach ($categories as $category): ?>
        <button type="button" data-cat-id="<?= (int) $category['id'] ?>"><?= esc($category['name']) ?></button>
      <?php endforeach; ?>
    </aside>
    <div id="product-grid" class="grid" aria-label="Catálogo"></div>
  </main>

  <footer class="cat-footer">
    <div class="cat-footer-inner">
      <div class="foot-brand">
        <?php if (! empty($business['logo'])): ?>
          <img class="foot-logo" src="<?= upload_url($business['logo']) ?>" alt="<?= esc($business['name']) ?>">
        <?php else: ?>
          <span class="foot-name"><?= esc($business['name']) ?></span>
        <?php endif; ?>
        <?php if ($business['tagline']): ?>
          <p class="foot-tag"><?= esc($business['tagline']) ?></p>
        <?php endif; ?>
      </div>

      <div>
        <h3 class="foot-title">Contacto</h3>
        <ul class="foot-list">
          <?php if ($business['phone']): ?>
            <li>
              <a href="https://wa.me/<?= esc(whatsapp_digits($business['phone'])) ?>" target="_blank" rel="noopener">
                <span class="material-symbols-outlined">call</span>
                <?= esc($business['phone']) ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($business['email']): ?>
            <li>
              <a href="mailto:<?= esc($business['email']) ?>">
                <span class="material-symbols-outlined">mail</span>
                <?= esc($business['email']) ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($business['address'] || $business['city']): ?>
            <li>
              <span>
                <span class="material-symbols-outlined">location_on</span>
                <?= esc(trim($business['address'] . ' ' . $business['city'])) ?>
              </span>
            </li>
          <?php endif; ?>
        </ul>
      </div>

      <div>
        <h3 class="foot-title">Síguenos</h3>
        <div class="foot-social">
          <?php if ($business['phone']): ?>
            <a href="https://wa.me/<?= esc(whatsapp_digits($business['phone'])) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.5 3.5A11 11 0 0 0 1.9 17.7L1 23l5.4-.9A11 11 0 1 0 20.5 3.5zm-8.5 17a9.1 9.1 0 0 1-4.6-1.3l-.3-.2-3.2.5.5-3.1-.2-.3A9.1 9.1 0 1 1 12 20.5zm5-6.8c-.3-.1-1.6-.8-1.9-.9s-.4-.1-.6.1-.7.9-.8 1.1-.3.2-.6.1a7.4 7.4 0 0 1-2.2-1.4 8.2 8.2 0 0 1-1.5-1.9c-.2-.3 0-.4.1-.6l.4-.5.3-.4c.1-.2 0-.3 0-.5l-.9-2.1c-.2-.6-.5-.5-.6-.5h-.5c-.2 0-.5.1-.7.3s-1 1-1 2.3 1 2.7 1.1 2.9 2 3 4.8 4.1a16 16 0 0 0 1.6.6 3.8 3.8 0 0 0 1.8.1c.5-.1 1.6-.7 1.9-1.3s.3-1.2.2-1.3c-.1-.2-.3-.2-.6-.3z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($business['instagram']): ?>
            <a href="<?= esc(str_starts_with($business['instagram'], 'http') ? $business['instagram'] : 'https://instagram.com/' . ltrim($business['instagram'], '@')) ?>" target="_blank" rel="noopener" aria-label="Instagram">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm11.3 1.2a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2.2A2.8 2.8 0 1 0 12 15a2.8 2.8 0 0 0 0-5.8z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($business['facebook']): ?>
            <a href="<?= esc(str_starts_with($business['facebook'], 'http') ? $business['facebook'] : 'https://facebook.com/' . ltrim($business['facebook'], '@')) ?>" target="_blank" rel="noopener" aria-label="Facebook">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($business['tiktok']): ?>
            <a href="<?= esc(str_starts_with($business['tiktok'], 'http') ? $business['tiktok'] : 'https://tiktok.com/@' . ltrim($business['tiktok'], '@')) ?>" target="_blank" rel="noopener" aria-label="TikTok">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14.5 3c.2 1.7.9 3.2 2.1 4.3 1.1 1 2.6 1.6 4.4 1.8v3.2c-1.5 0-3-.4-4.3-1.1v6.3A6.9 6.9 0 1 1 11 10.7v3.3a3.7 3.7 0 1 0 2.6 3.5V3h.9z"/></svg>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="cat-footer-bar">
      <div class="cat-footer-bar-inner">
        <span>© <?= date('Y') ?> <?= esc($business['name']) ?></span>
        <?php if ($business['city']): ?>
          <span><?= esc($business['city']) ?></span>
        <?php endif; ?>
      </div>
      <?= view('partials/brand_footer', ['brandColor' => $primary, 'onDark' => true]) ?>
    </div>
  </footer>

  <div class="overlay" id="overlay"></div>
  <aside class="drawer" id="quote-drawer" aria-label="Tu cotización">
    <header>
      <h2>Tu cotización</h2>
      <button class="icon-btn" type="button" data-close-cart aria-label="Cerrar">
        <span class="material-symbols-outlined">close</span>
      </button>
    </header>
    <div class="drawer-body">
      <div id="quote-empty" class="empty-cat" style="display:flex;flex-direction:column;align-items:center">
        <span class="material-symbols-outlined" style="font-size:42px;color:#a8a29a">shopping_cart</span>
        <strong>Lista vacía</strong>
        <p>Agrega productos del catálogo para armar tu cotización.</p>
      </div>
      <div id="quote-list"></div>
    </div>
    <div class="drawer-foot" hidden>
      <div class="quote-total" id="quote-total" hidden>
        <div class="quote-total-row" id="quote-retail-row" hidden>
          <span>Total cotización</span>
          <span id="quote-retail-value"></span>
        </div>
        <div class="quote-total-row">
          <span id="quote-pay-label">Total a pagar</span>
          <strong id="quote-total-value"></strong>
        </div>
      </div>
      <p class="quote-min" id="quote-min" hidden></p>
      <button class="wa-btn" id="wa-send" type="button" disabled>Enviar a <?= esc($advisor['name']) ?> por WhatsApp</button>
      <button class="add-more-btn" type="button" data-add-more>
        <span class="material-symbols-outlined">add_shopping_cart</span>
        Agregar más productos
      </button>
      <p class="quote-hint">Un asesor confirma precio final y disponibilidad.</p>
      <button class="clear-list-btn" type="button" data-clear-cart>Vaciar lista</button>
    </div>
  </aside>

  <div class="modal done-modal" id="done-modal" role="dialog" aria-labelledby="done-title" aria-modal="true">
    <div class="modal-card done-card">
      <span class="done-icon material-symbols-outlined icon-fill" aria-hidden="true">check_circle</span>
      <h2 id="done-title">Cotización enviada</h2>
      <p>Ya se abrió WhatsApp con <?= esc($advisor['name']) ?>. Ahí confirman disponibilidad, precio final y cierran la compra.</p>
      <p class="muted">Puedes seguir viendo el catálogo o cerrar esta página cuando quieras.</p>
      <div class="done-actions">
        <button class="quote-btn" type="button" data-close-done>Seguir viendo el catálogo</button>
        <?php if ($canChangeAdvisor): ?>
          <a class="done-secondary" href="<?= esc($pickUrl) ?>">Elegir otro asesor</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="modal" id="product-modal">
    <div class="modal-card">
      <header>
        <h2 data-modal-title></h2>
        <button class="icon-btn" type="button" data-close-modal aria-label="Cerrar">
          <span class="material-symbols-outlined">close</span>
        </button>
      </header>
      <div data-modal-img></div>
      <div style="padding:16px 20px 20px">
        <p class="sku" data-modal-meta></p>
        <div data-modal-colors></div>
        <div data-modal-sizes></div>
        <p class="muted" data-modal-desc></p>
        <button class="add-btn" type="button" data-modal-add data-add="">Agregar a cotización</button>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/catalog.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/catalog.js') ?>"></script>
  <script src="<?= base_url('assets/js/pwa.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/pwa.js') ?>"></script>
</body>
</html>
