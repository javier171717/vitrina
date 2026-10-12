<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Productos</h1>
    <p>Este es el portafolio que ven tus clientes.</p>
  </div>
  <a class="btn btn-primary" href="<?= site_url('panel/productos/nuevo') ?>">Nuevo producto</a>
</div>

<div class="card">
  <?php if (! $products): ?>
    <div class="empty">Todavía no hay productos. Sube el primero con foto y referencia.</div>
  <?php else: ?>
    <div class="table-wrap product-table">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Detal</th>
            <th>Mayor</th>
            <th>Estado</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <td>
              <div class="product-cell">
                <?php if (! empty($product['image'])): ?>
                  <img class="thumb" src="<?= upload_url($product['image']) ?>" alt="">
                <?php else: ?>
                  <span class="thumb thumb-empty"></span>
                <?php endif; ?>
                <div>
                  <strong><?= esc($product['name']) ?></strong>
                  <?php if ($product['sku']): ?><div class="hint">Ref. <?= esc($product['sku']) ?></div><?php endif; ?>
                </div>
              </div>
            </td>
            <td data-label="Categoría"><?= esc($product['category_name'] ?: 'Sin categoría') ?></td>
            <td data-label="Detal"><?= $product['price'] !== null ? esc(money_cop($product['price'])) : '—' ?></td>
            <td data-label="Mayor">
              <?php if ($product['wholesale_price'] !== null): ?>
                <?= esc(money_cop($product['wholesale_price'])) ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td data-label="Estado">
              <span class="badge <?= $product['is_active'] ? 'badge-ok' : 'badge-off' ?>">
                <?= $product['is_active'] ? 'Visible' : 'Oculto' ?></span>
            </td>
            <td class="row-actions">
              <a class="btn btn-ghost btn-sm" href="<?= site_url('panel/productos/' . $product['id'] . '/editar') ?>">Editar</a>
              <form method="post" action="<?= site_url('panel/productos/' . $product['id'] . '/eliminar') ?>" onsubmit="return confirm('¿Eliminar este producto?')">
                <?= csrf_field() ?>
                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>
