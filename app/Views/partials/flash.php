<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('errors')): ?>
  <ul class="error-list">
    <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
      <li><?= esc($error) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
