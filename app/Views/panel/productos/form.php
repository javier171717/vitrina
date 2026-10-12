<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1><?= $product ? 'Editar producto' : 'Nuevo producto' ?></h1>
    <p>La foto, la referencia y la compatibilidad ayudan a cotizar más rápido.</p>
  </div>
  <a class="btn btn-ghost" href="<?= site_url('panel/productos') ?>">Volver</a>
</div>

<div class="card">
  <form method="post" action="<?= $product ? site_url('panel/productos/' . $product['id']) : site_url('panel/productos') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="name">Nombre</label>
        <input class="input" id="name" name="name" value="<?= esc(old('name', $product['name'] ?? '')) ?>" required>
      </div>
      <div class="field">
        <label for="sku">Referencia / SKU</label>
        <input class="input" id="sku" name="sku" value="<?= esc(old('sku', $product['sku'] ?? '')) ?>">
      </div>
      <div class="field full">
        <label for="category_id">Categoría</label>
        <div class="category-row">
          <select class="input" id="category_id" name="category_id"
            data-category-url="<?= esc(site_url('panel/categorias'), 'attr') ?>"
            data-csrf-name="<?= esc(csrf_token(), 'attr') ?>"
            data-csrf-hash="<?= esc(csrf_hash(), 'attr') ?>"
            data-csrf-header="<?= esc(csrf_header(), 'attr') ?>">
            <option value="">— Sin categoría —</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>" <?= (string) old('category_id', $product['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                <?= esc($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-ghost" type="button" data-new-category>+ Nueva categoría</button>
        </div>
        <div class="category-create" id="category-create" hidden>
          <input class="input" id="new-category-name" type="text" maxlength="120" placeholder="Nombre de la categoría" autocomplete="off">
          <button class="btn btn-primary" type="button" data-save-category>Crear</button>
        </div>
        <p class="hint" id="category-create-error" hidden></p>
      </div>
      <div class="field full">
        <label>Colores</label>
        <p class="hint">Si el mismo producto sale en varios colores, agrégalos. En el catálogo se ven como etiquetas.</p>
        <?php
          $productColors = parse_product_colors(old('colors', $product['colors'] ?? []));
          $colorPresets  = ['Negro', 'Dorado', 'Plateado', 'Rojo', 'Azul', 'Blanco', 'Verde', 'Naranja'];
        ?>
        <div class="color-presets" id="product-colors">
          <?php foreach ($colorPresets as $preset): ?>
            <?php $on = in_array($preset, $productColors, true); ?>
            <button type="button" class="color-tag<?= $on ? ' is-on' : '' ?>" data-color-preset="<?= esc($preset, 'attr') ?>" style="--swatch: <?= esc(product_color_hex($preset), 'attr') ?>"><?= esc($preset) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="color-create">
          <input class="input" id="custom-color-name" type="text" maxlength="40" placeholder="Otro color, ej. Cromado" autocomplete="off">
          <button class="btn btn-ghost" type="button" data-add-color>+ Agregar</button>
        </div>
        <div class="color-selected" id="color-selected">
          <?php foreach ($productColors as $color): ?>
            <span class="color-tag is-on" style="--swatch: <?= esc(product_color_hex($color), 'attr') ?>">
              <?= esc($color) ?>
              <button type="button" class="color-tag-x" data-remove-color="<?= esc($color, 'attr') ?>" aria-label="Quitar <?= esc($color) ?>">×</button>
            </span>
          <?php endforeach; ?>
        </div>
        <div id="product-colors-inputs">
          <?php foreach ($productColors as $color): ?>
            <input type="hidden" name="colors[]" value="<?= esc($color) ?>">
          <?php endforeach; ?>
        </div>
      </div>
      <div class="field full">
        <label>Tallas</label>
        <p class="hint">Si vendes ropa u otros productos con talla, agrégalas. En el catálogo se ven como etiquetas.</p>
        <?php
          $productSizes = parse_product_colors(old('sizes', $product['sizes'] ?? []));
          $sizePresets  = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36'];
        ?>
        <div class="color-presets" id="product-sizes">
          <?php foreach ($sizePresets as $preset): ?>
            <?php $on = in_array($preset, $productSizes, true); ?>
            <button type="button" class="color-tag<?= $on ? ' is-on' : '' ?>" data-size-preset="<?= esc($preset, 'attr') ?>"><?= esc($preset) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="color-create">
          <input class="input" id="custom-size-name" type="text" maxlength="20" placeholder="Otra talla, ej. 38 o Única" autocomplete="off">
          <button class="btn btn-ghost" type="button" data-add-size>+ Agregar</button>
        </div>
        <div class="color-selected" id="size-selected">
          <?php foreach ($productSizes as $size): ?>
            <span class="color-tag is-on">
              <?= esc($size) ?>
              <button type="button" class="color-tag-x" data-remove-size="<?= esc($size, 'attr') ?>" aria-label="Quitar <?= esc($size) ?>">×</button>
            </span>
          <?php endforeach; ?>
        </div>
        <div id="product-sizes-inputs">
          <?php foreach ($productSizes as $size): ?>
            <input type="hidden" name="sizes[]" value="<?= esc($size) ?>">
          <?php endforeach; ?>
        </div>
      </div>
      <div class="field">
        <label for="price">Precio al detal</label>
        <input class="input" id="price" name="price" inputmode="numeric" placeholder="20.000" value="<?= esc(old('price', format_price_cop($product['price'] ?? ''))) ?>">
      </div>
      <div class="field">
        <label for="wholesale_price">Precio al por mayor</label>
        <input class="input" id="wholesale_price" name="wholesale_price" inputmode="numeric" placeholder="20.000" value="<?= esc(old('wholesale_price', format_price_cop($product['wholesale_price'] ?? ''))) ?>">
        <p class="hint">Se cobra cuando la cotización llega al monto configurado en <a href="<?= site_url('panel/negocio') ?>">Mi negocio</a>.</p>
      </div>
      <div class="field full">
        <label for="compatible">Compatible con / búsqueda</label>
        <input class="input" id="compatible" name="compatible" value="<?= esc(old('compatible', $product['compatible'] ?? '')) ?>" placeholder="AKT 125, Honda CB, Pulsar 180">
      </div>
      <div class="field full">
        <label for="description">Descripción</label>
        <textarea id="description" name="description"><?= esc(old('description', $product['description'] ?? '')) ?></textarea>
      </div>
      <div class="field">
        <label for="image">Foto</label>
        <input class="input" type="file" id="image" name="image" accept="image/*">
        <?php if (! empty($product['image'])): ?>
          <img class="preview" src="<?= upload_url($product['image']) ?>" alt="" style="margin-top:8px">
        <?php endif; ?>
      </div>
      <div class="field">
        <label class="check" style="margin-top:28px">
          <input type="checkbox" name="is_active" value="1" <?= old('is_active', $product['is_active'] ?? 1) ? 'checked' : '' ?>>
          Visible en el catálogo
        </label>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Guardar producto</button>
  </form>
</div>
<?= $this->endSection() ?>
