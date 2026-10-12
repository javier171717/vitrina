<?= $this->extend('layouts/panel') ?>
<?= $this->section('content') ?>
<div class="topbar">
  <div>
    <h1>Categorías</h1>
    <p>Organizan el portafolio en el catálogo público.</p>
  </div>
</div>

<div class="card" style="margin-bottom:16px">
  <form method="post" action="<?= site_url('panel/categorias') ?>" class="share-box">
    <?= csrf_field() ?>
    <input class="input" name="name" placeholder="Nombre de la categoría" value="<?= esc(old('name')) ?>" required>
    <button class="btn btn-primary" type="submit">Agregar</button>
  </form>
</div>

<div class="card">
  <?php if (! $categories): ?>
    <div class="empty">Crea la primera categoría, por ejemplo Lubricantes o Accesorios.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Nombre</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
          <tr>
            <td>
              <form method="post" action="<?= site_url('panel/categorias/' . $category['id']) ?>" class="share-box">
                <?= csrf_field() ?>
                <input class="input" name="name" value="<?= esc($category['name']) ?>" required>
                <button class="btn btn-ghost btn-sm" type="submit">Guardar</button>
              </form>
            </td>
            <td>
              <form method="post" action="<?= site_url('panel/categorias/' . $category['id'] . '/eliminar') ?>" onsubmit="return confirm('¿Eliminar esta categoría? Los productos quedan sin categoría.')">
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
