(() => {
  const btn = document.querySelector('[data-menu]');
  const sidebar = document.querySelector('.sidebar');
  const backdrop = document.querySelector('.backdrop');
  btn?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    backdrop?.classList.toggle('show');
  });
  backdrop?.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    backdrop?.classList.remove('show');
  });

  document.querySelectorAll('[data-copy]').forEach((el) => {
    el.addEventListener('click', async () => {
      const input = document.querySelector(el.dataset.copy);
      if (!input) return;
      try {
        await navigator.clipboard.writeText(input.value);
        el.textContent = 'Copiado';
        setTimeout(() => { el.textContent = 'Copiar'; }, 1600);
      } catch {
        input.select();
        document.execCommand('copy');
      }
    });
  });
  document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.togglePassword);
      const icon = btn.querySelector('.material-symbols-outlined');
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      if (icon) icon.textContent = show ? 'visibility_off' : 'visibility';
      btn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
  });

  const passwordForm = document.getElementById('password-form');
  const passwordClientError = document.getElementById('password-client-error');
  passwordForm?.addEventListener('submit', (event) => {
    const current = document.getElementById('current_password')?.value.trim() ?? '';
    const next = document.getElementById('new_password')?.value ?? '';
    const confirm = document.getElementById('confirm_password')?.value ?? '';
    let message = '';

    if (!current) {
      message = 'No se cambió la contraseña porque falta la contraseña actual.';
    } else if (next.length < 8) {
      message = 'No se cambió la contraseña porque la nueva debe tener al menos 8 caracteres.';
    } else if (next !== confirm) {
      message = 'No se cambió la contraseña porque la confirmación no coincide con la nueva.';
    } else if (current === next) {
      message = 'No se cambió la contraseña porque la nueva es igual a la actual. Elige una distinta.';
    }

    if (message) {
      event.preventDefault();
      if (passwordClientError) {
        passwordClientError.hidden = false;
        passwordClientError.textContent = message;
      }
    } else if (passwordClientError) {
      passwordClientError.hidden = true;
      passwordClientError.textContent = '';
    }
  });

  const confirmOverlay = () => {
    const existing = document.getElementById('confirm-overlay');
    existing?.remove();

    const overlay = document.createElement('div');
    overlay.className = 'plan-overlay';
    overlay.id = 'confirm-overlay';
    overlay.innerHTML = `
      <div class="plan-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <div class="plan-modal-icon">
          <span class="material-symbols-outlined confirm-icon" style="font-size:28px;color:var(--primary)">info</span>
        </div>
        <h3 class="plan-modal-title" id="confirm-title"></h3>
        <div class="plan-modal-body">
          <p class="confirm-message"></p>
          <p class="confirm-question" style="margin-top:14px;font-weight:700">¿Está seguro de continuar?</p>
        </div>
        <div class="plan-modal-actions">
          <button class="btn btn-ghost" type="button" data-confirm-cancel>Cancelar</button>
          <button class="btn btn-primary" type="button" data-confirm-ok>OK</button>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);
    return overlay;
  };

  const showConfirm = ({ title, message, okLabel, danger, onConfirm }) => {
    const overlay = confirmOverlay();
    const modal = overlay.querySelector('.plan-modal');
    const icon = overlay.querySelector('.confirm-icon');
    const okBtn = overlay.querySelector('[data-confirm-ok]');

    overlay.querySelector('#confirm-title').textContent = title;
    overlay.querySelector('.confirm-message').textContent = message;
    okBtn.textContent = okLabel;

    if (danger) {
      modal.classList.add('plan-modal-expired');
      icon.style.color = 'var(--danger)';
      okBtn.classList.remove('btn-primary');
      okBtn.classList.add('btn-danger');
    }

    const close = () => overlay.remove();
    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) close();
    });
    overlay.querySelector('[data-confirm-cancel]').addEventListener('click', close);
    okBtn.addEventListener('click', () => {
      close();
      onConfirm();
    });
  };

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (form.dataset.confirmed === '1') return;
      event.preventDefault();
      showConfirm({
        title: form.dataset.confirmTitle || 'Información del sistema',
        message: form.dataset.confirmMessage || '',
        okLabel: form.dataset.confirmOk || 'OK',
        danger: form.dataset.confirmDanger === '1',
        onConfirm: () => {
          form.dataset.confirmed = '1';
          form.submit();
        },
      });
    });
  });

  const colorInput = document.getElementById('primary_color');
  const brandCredit = document.querySelector('.brand-credit');
  colorInput?.addEventListener('input', () => {
    if (brandCredit && /^#[0-9A-Fa-f]{6}$/.test(colorInput.value)) {
      brandCredit.style.setProperty('--brand-accent', colorInput.value);
    }
  });

  const categorySelect = document.getElementById('category_id');
  const categoryCreate = document.getElementById('category-create');
  const categoryName = document.getElementById('new-category-name');
  const categoryError = document.getElementById('category-create-error');
  const openCategoryBtn = document.querySelector('[data-new-category]');
  const saveCategoryBtn = document.querySelector('[data-save-category]');

  const setCategoryError = (message) => {
    if (!categoryError) return;
    categoryError.hidden = !message;
    categoryError.textContent = message || '';
  };

  const refreshCsrf = (hash) => {
    if (!hash) return;
    if (categorySelect) categorySelect.dataset.csrfHash = hash;
    document.querySelectorAll('input[name="' + (categorySelect?.dataset.csrfName || 'csrf_test_name') + '"]').forEach((input) => {
      input.value = hash;
    });
  };

  const createCategory = async () => {
    if (!categorySelect || !categoryName) return;
    const name = categoryName.value.trim();
    if (name.length < 2) {
      setCategoryError('Escribe un nombre de categoría (mínimo 2 caracteres).');
      categoryName.focus();
      return;
    }

    const csrfName = categorySelect.dataset.csrfName;
    const csrfHash = categorySelect.dataset.csrfHash;
    const csrfHeader = categorySelect.dataset.csrfHeader || 'X-CSRF-TOKEN';
    saveCategoryBtn && (saveCategoryBtn.disabled = true);

    try {
      const res = await fetch(categorySelect.dataset.categoryUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          [csrfHeader]: csrfHash,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
          [csrfName]: csrfHash,
          name,
        }),
      });
      const data = await res.json().catch(() => ({}));
      refreshCsrf(data.csrf);
      if (!res.ok || !data.ok) {
        throw new Error(data.error || 'No se pudo crear la categoría.');
      }

      const id = String(data.id);
      let option = [...categorySelect.options].find((opt) => opt.value === id);
      if (!option) {
        option = document.createElement('option');
        option.value = id;
        option.textContent = data.name;
        categorySelect.appendChild(option);
      } else {
        option.textContent = data.name;
      }
      categorySelect.value = id;
      categoryName.value = '';
      categoryCreate.hidden = true;
      setCategoryError('');
    } catch (err) {
      setCategoryError(err.message || 'No se pudo crear la categoría.');
    } finally {
      saveCategoryBtn && (saveCategoryBtn.disabled = false);
    }
  };

  openCategoryBtn?.addEventListener('click', () => {
    if (!categoryCreate) return;
    categoryCreate.hidden = false;
    setCategoryError('');
    categoryName?.focus();
  });
  saveCategoryBtn?.addEventListener('click', createCategory);
  categoryName?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      createCategory();
    }
    if (event.key === 'Escape') {
      categoryCreate.hidden = true;
      setCategoryError('');
    }
  });

  const colorRoot = document.getElementById('product-colors');
  const colorSelected = document.getElementById('color-selected');
  const colorInputs = document.getElementById('product-colors-inputs');
  const colorCustom = document.getElementById('custom-color-name');
  const SWATCH = {
    negro: '#171717', black: '#171717',
    dorado: '#c9a227', oro: '#c9a227', gold: '#c9a227',
    plateado: '#6b7280', plata: '#6b7280', silver: '#6b7280', cromado: '#94a3b8',
    rojo: '#dc2626', red: '#dc2626',
    azul: '#2563eb', blue: '#2563eb',
    blanco: '#9ca3af', white: '#9ca3af',
    verde: '#16a34a', green: '#16a34a',
    naranja: '#ea580c', orange: '#ea580c',
    gris: '#6b7280', gray: '#6b7280', grey: '#6b7280',
    rosa: '#db2777', pink: '#db2777',
    amarillo: '#ca8a04', yellow: '#ca8a04',
    morado: '#7c3aed', violeta: '#7c3aed', purple: '#7c3aed',
    cafe: '#92400e', café: '#92400e', marron: '#92400e', marrón: '#92400e', brown: '#92400e',
  };
  const colorHex = (name) => SWATCH[String(name || '').trim().toLowerCase()] || '#4b5563';
  const selectedColors = () => [...(colorInputs?.querySelectorAll('input[name="colors[]"]') || [])].map((el) => el.value);
  const hasColor = (name) => selectedColors().some((c) => c.toLowerCase() === String(name).trim().toLowerCase());

  const renderProductColors = () => {
    if (!colorSelected || !colorInputs || !colorRoot) return;
    const colors = selectedColors();
    colorSelected.innerHTML = colors.map((c) => `
      <span class="color-tag is-on" style="--swatch:${colorHex(c)}">
        ${c.replace(/</g, '&lt;')}
        <button type="button" class="color-tag-x" data-remove-color="${c.replace(/"/g, '&quot;')}" aria-label="Quitar ${c.replace(/"/g, '&quot;')}">×</button>
      </span>
    `).join('');
    colorRoot.querySelectorAll('[data-color-preset]').forEach((btn) => {
      btn.classList.toggle('is-on', hasColor(btn.dataset.colorPreset));
    });
  };

  const addProductColor = (name) => {
    const color = String(name || '').trim();
    if (color.length < 2 || hasColor(color) || selectedColors().length >= 12) return;
    if (!colorInputs) return;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'colors[]';
    input.value = color;
    colorInputs.appendChild(input);
    renderProductColors();
  };

  const removeProductColor = (name) => {
    const key = String(name || '').trim().toLowerCase();
    colorInputs?.querySelectorAll('input[name="colors[]"]').forEach((el) => {
      if (el.value.trim().toLowerCase() === key) el.remove();
    });
    renderProductColors();
  };

  colorRoot?.addEventListener('click', (event) => {
    const preset = event.target.closest('[data-color-preset]');
    if (!preset) return;
    const name = preset.dataset.colorPreset || '';
    if (hasColor(name)) removeProductColor(name);
    else addProductColor(name);
  });
  colorSelected?.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-remove-color]');
    if (btn) removeProductColor(btn.dataset.removeColor);
  });
  document.querySelector('[data-add-color]')?.addEventListener('click', () => {
    addProductColor(colorCustom?.value || '');
    if (colorCustom) colorCustom.value = '';
    colorCustom?.focus();
  });
  colorCustom?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      addProductColor(colorCustom.value);
      colorCustom.value = '';
    }
  });

  const sizeRoot = document.getElementById('product-sizes');
  const sizeSelected = document.getElementById('size-selected');
  const sizeInputs = document.getElementById('product-sizes-inputs');
  const sizeCustom = document.getElementById('custom-size-name');
  const selectedSizes = () => [...(sizeInputs?.querySelectorAll('input[name="sizes[]"]') || [])].map((el) => el.value);
  const hasSize = (name) => selectedSizes().some((s) => s.toLowerCase() === String(name).trim().toLowerCase());

  const renderProductSizes = () => {
    if (!sizeSelected || !sizeInputs || !sizeRoot) return;
    const sizes = selectedSizes();
    sizeSelected.innerHTML = sizes.map((s) => `
      <span class="color-tag is-on">
        ${s.replace(/</g, '&lt;')}
        <button type="button" class="color-tag-x" data-remove-size="${s.replace(/"/g, '&quot;')}" aria-label="Quitar ${s.replace(/"/g, '&quot;')}">×</button>
      </span>
    `).join('');
    sizeRoot.querySelectorAll('[data-size-preset]').forEach((btn) => {
      btn.classList.toggle('is-on', hasSize(btn.dataset.sizePreset));
    });
  };

  const addProductSize = (name) => {
    const size = String(name || '').trim();
    if (size.length < 1 || hasSize(size) || selectedSizes().length >= 20) return;
    if (!sizeInputs) return;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'sizes[]';
    input.value = size;
    sizeInputs.appendChild(input);
    renderProductSizes();
  };

  const removeProductSize = (name) => {
    const key = String(name || '').trim().toLowerCase();
    sizeInputs?.querySelectorAll('input[name="sizes[]"]').forEach((el) => {
      if (el.value.trim().toLowerCase() === key) el.remove();
    });
    renderProductSizes();
  };

  sizeRoot?.addEventListener('click', (event) => {
    const preset = event.target.closest('[data-size-preset]');
    if (!preset) return;
    const name = preset.dataset.sizePreset || '';
    if (hasSize(name)) removeProductSize(name);
    else addProductSize(name);
  });
  sizeSelected?.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-remove-size]');
    if (btn) removeProductSize(btn.dataset.removeSize);
  });
  document.querySelector('[data-add-size]')?.addEventListener('click', () => {
    addProductSize(sizeCustom?.value || '');
    if (sizeCustom) sizeCustom.value = '';
    sizeCustom?.focus();
  });
  sizeCustom?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      addProductSize(sizeCustom.value);
      sizeCustom.value = '';
    }
  });
})();
