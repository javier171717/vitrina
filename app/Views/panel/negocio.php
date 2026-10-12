<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Mi negocio</h1>
    <p>Así se ve tu catálogo: nombre, color, logo y datos de contacto.</p>
  </div>
</div>

<div class="card">
  <form method="post" action="<?= site_url('panel/negocio') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="name">Nombre del negocio</label>
        <input class="input" id="name" name="name" value="<?= esc(old('name', $business['name'])) ?>" required>
      </div>
      <div class="field">
        <label for="slug">URL del catálogo</label>
        <input class="input" id="slug" name="slug" value="<?= esc(old('slug', $business['slug'])) ?>" required>
        <p class="hint"><?= catalog_url(old('slug', $business['slug'])) ?></p>
      </div>
      <div class="field full">
        <label for="tagline">Frase corta</label>
        <input class="input" id="tagline" name="tagline" value="<?= esc(old('tagline', $business['tagline'])) ?>" placeholder="Lubricantes, repuestos y accesorios">
      </div>
      <div class="field">
        <label for="primary_color">Color principal</label>
        <input class="input" type="color" id="primary_color" name="primary_color" value="<?= esc(old('primary_color', $business['primary_color'] ?: '#e30613')) ?>">
      </div>
      <div class="field">
        <label class="check" style="margin-top:28px">
          <input type="checkbox" name="show_prices" value="1" <?= old('show_prices', $business['show_prices']) ? 'checked' : '' ?>>
          Mostrar precios en el catálogo
        </label>
      </div>
      <div class="field full">
        <label for="wholesale_min_total">Monto para precio al por mayor</label>
        <input class="input" id="wholesale_min_total" name="wholesale_min_total" inputmode="numeric" placeholder="301.000" value="<?= esc(old('wholesale_min_total', format_price_cop($business['wholesale_min_total'] ?? ''))) ?>">
        <p class="hint">Si la cotización llega a este valor (sumando precios al detal), todos los productos con precio mayorista pasan a ese precio. Déjalo vacío para no usarlo.</p>
      </div>
      <div class="field">
        <label for="phone">Teléfono / WhatsApp del negocio</label>
        <input class="input" id="phone" name="phone" value="<?= esc(old('phone', $business['phone'])) ?>">
      </div>
      <div class="field">
        <label for="email">Correo</label>
        <input class="input" type="email" id="email" name="email" value="<?= esc(old('email', $business['email'])) ?>">
      </div>
      <div class="field">
        <label for="city">Ciudad</label>
        <input class="input" id="city" name="city" value="<?= esc(old('city', $business['city'])) ?>">
      </div>
      <div class="field">
        <label for="address">Dirección</label>
        <input class="input" id="address" name="address" value="<?= esc(old('address', $business['address'])) ?>">
      </div>
      <div class="field">
        <label for="instagram">Instagram</label>
        <input class="input" id="instagram" name="instagram" value="<?= esc(old('instagram', $business['instagram'])) ?>" placeholder="https://instagram.com/...">
      </div>
      <div class="field">
        <label for="facebook">Facebook</label>
        <input class="input" id="facebook" name="facebook" value="<?= esc(old('facebook', $business['facebook'])) ?>">
      </div>
      <div class="field">
        <label for="tiktok">TikTok</label>
        <input class="input" id="tiktok" name="tiktok" value="<?= esc(old('tiktok', $business['tiktok'])) ?>">
      </div>
      <div class="field">
        <label for="logo">Logo</label>
        <input class="input" type="file" id="logo" name="logo" accept="image/*">
        <?php if (! empty($business['logo'])): ?>
          <div class="preview-row">
            <img class="preview" src="<?= upload_url($business['logo']) ?>" alt="Logo">
            <button class="btn btn-danger btn-sm" type="submit" name="remove_image" value="logo">Quitar</button>
          </div>
        <?php endif; ?>
      </div>
      <div class="field">
        <label for="banner">Banner escritorio</label>
        <input class="input" type="file" id="banner" name="banner" accept="image/*">
        <?php if (! empty($business['banner'])): ?>
          <div class="preview-row">
            <img class="preview" src="<?= upload_url($business['banner']) ?>" alt="Banner">
            <button class="btn btn-danger btn-sm" type="submit" name="remove_image" value="banner">Quitar</button>
          </div>
        <?php endif; ?>
      </div>
      <div class="field">
        <label for="banner_mobile">Banner celular</label>
        <input class="input" type="file" id="banner_mobile" name="banner_mobile" accept="image/*">
        <?php if (! empty($business['banner_mobile'])): ?>
          <div class="preview-row">
            <img class="preview" src="<?= upload_url($business['banner_mobile']) ?>" alt="Banner móvil">
            <button class="btn btn-danger btn-sm" type="submit" name="remove_image" value="banner_mobile">Quitar</button>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Guardar cambios</button>
  </form>
</div>

<div class="card" id="password" style="margin-top:16px">
  <h3 style="margin-top:0">Cambiar contraseña</h3>
  <p class="hint" style="margin-top:0">Esta es la contraseña con la que entras al panel (<?= esc($user['email'] ?? '') ?>).</p>

  <?php if (session()->getFlashdata('password_error')): ?>
    <div class="alert alert-error"><?= esc(session()->getFlashdata('password_error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('password_success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('password_success')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('password_errors')): ?>
    <div class="alert alert-error">
      No se cambió la contraseña:
      <ul class="error-list" style="margin:8px 0 0">
        <?php foreach ((array) session()->getFlashdata('password_errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <div class="alert alert-error" id="password-client-error" hidden></div>

  <form method="post" action="<?= site_url('panel/negocio/password') ?>" id="password-form">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field full">
        <label for="current_password">Contraseña actual</label>
        <div class="password-wrap">
          <input class="input" type="password" id="current_password" name="current_password" required autocomplete="current-password">
          <button class="password-toggle" type="button" data-toggle-password="current_password" aria-label="Mostrar contraseña">
            <span class="material-symbols-outlined">visibility</span>
          </button>
        </div>
      </div>
      <div class="field">
        <label for="new_password">Nueva contraseña</label>
        <div class="password-wrap">
          <input class="input" type="password" id="new_password" name="new_password" minlength="8" required autocomplete="new-password">
          <button class="password-toggle" type="button" data-toggle-password="new_password" aria-label="Mostrar contraseña">
            <span class="material-symbols-outlined">visibility</span>
          </button>
        </div>
        <p class="hint">Mínimo 8 caracteres.</p>
      </div>
      <div class="field">
        <label for="confirm_password">Confirmar nueva contraseña</label>
        <div class="password-wrap">
          <input class="input" type="password" id="confirm_password" name="confirm_password" minlength="8" required autocomplete="new-password">
          <button class="password-toggle" type="button" data-toggle-password="confirm_password" aria-label="Mostrar contraseña">
            <span class="material-symbols-outlined">visibility</span>
          </button>
        </div>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
  </form>
</div>
<?= $this->endSection() ?>
