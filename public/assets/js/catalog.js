(() => {
  const root = document.getElementById('catalog-app');
  if (!root) return;

  const products = JSON.parse(document.getElementById('catalog-products')?.textContent || '[]');
  const business = JSON.parse(document.getElementById('catalog-business')?.textContent || '{}');
  const advisor = JSON.parse(document.getElementById('catalog-advisor')?.textContent || '{}');
  const showPrices = root.dataset.showPrices === '1';
  const storageKey = `quote:${business.slug}:${advisor.slug}`;

  const state = {
    q: '',
    categoryId: 0,
    cart: loadCart(),
    picked: {},
    pickedSize: {},
  };

  const els = {
    grid: document.getElementById('product-grid'),
    search: document.getElementById('catalog-search'),
    cats: document.querySelectorAll('[data-cat-id]'),
    count: document.getElementById('cart-count'),
    drawer: document.getElementById('quote-drawer'),
    overlay: document.getElementById('overlay'),
    quoteList: document.getElementById('quote-list'),
    emptyQuote: document.getElementById('quote-empty'),
    wa: document.getElementById('wa-send'),
    modal: document.getElementById('product-modal'),
    done: document.getElementById('done-modal'),
    status: document.getElementById('catalog-status'),
    quoteTotal: document.getElementById('quote-total'),
    quoteTotalValue: document.getElementById('quote-total-value'),
    quotePayLabel: document.getElementById('quote-pay-label'),
    quoteRetailRow: document.getElementById('quote-retail-row'),
    quoteRetailValue: document.getElementById('quote-retail-value'),
    quoteMin: document.getElementById('quote-min'),
    quoteBtnTotal: document.getElementById('quote-btn-total'),
    drawerFoot: document.querySelector('#quote-drawer .drawer-foot'),
  };

  function loadCart() {
    try {
      const parsed = JSON.parse(localStorage.getItem(storageKey) || '[]');
      if (!Array.isArray(parsed)) return [];
      return parsed
        .map((item) => ({
          id: Number(item.id),
          qty: Number(item.qty),
          color: String(item.color || '').trim(),
          size: String(item.size || '').trim(),
        }))
        .filter((item) => item.id > 0 && item.qty > 0);
    } catch {
      return [];
    }
  }

  function saveCart() {
    localStorage.setItem(storageKey, JSON.stringify(state.cart));
  }

  function money(value) {
    if (value == null) return '';
    return '$ ' + Math.round(Number(value)).toLocaleString('es-CO');
  }

  function retailCartTotal() {
    return state.cart.reduce((sum, item) => {
      const product = products.find((x) => x.id === item.id);
      if (!product || product.price == null) return sum;
      return sum + Number(product.price) * item.qty;
    }, 0);
  }

  function cartWholesaleByTotal() {
    const min = Number(business.wholesale_min_total || 0);
    return min > 0 && retailCartTotal() >= min;
  }

  function unitPrice(product, qty, forceWholesale) {
    const retail = product.price == null ? null : Number(product.price);
    const wholesale = product.wholesale_price == null ? null : Number(product.wholesale_price);
    if (wholesale != null && forceWholesale) return wholesale;
    return retail;
  }

  function matches(product) {
    if (state.categoryId && product.category_id !== state.categoryId) return false;
    const q = state.q.trim().toLowerCase();
    if (!q) return true;
    return [product.name, product.sku, product.compatible, product.description, product.category_name]
      .concat(Array.isArray(product.colors) ? product.colors : [])
      .concat(Array.isArray(product.sizes) ? product.sizes : [])
      .filter(Boolean)
      .join(' ')
      .toLowerCase()
      .includes(q);
  }

  function productColors(product) {
    return Array.isArray(product?.colors) ? product.colors.filter(Boolean) : [];
  }

  function colorHex(name) {
    const key = String(name || '').trim().toLowerCase();
    const map = {
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
    return map[key] || '#4b5563';
  }

  function pickedColor(product) {
    const colors = productColors(product);
    if (!colors.length) return '';
    const current = state.picked[product.id];
    if (current && colors.some((c) => c === current)) return current;
    return colors[0];
  }

  function colorTagsHtml(product) {
    const colors = productColors(product);
    if (!colors.length) return '';
    const selected = pickedColor(product);
    return `<div class="color-tags">${colors.map((c) => `
      <button type="button" class="color-tag${c === selected ? ' is-on' : ''}" data-color-pick="${product.id}" data-color="${escapeHtml(c)}" style="--swatch:${colorHex(c)}">${escapeHtml(c)}</button>
    `).join('')}</div>`;
  }

  function productSizes(product) {
    return Array.isArray(product?.sizes) ? product.sizes.filter(Boolean) : [];
  }

  function pickedSize(product) {
    const sizes = productSizes(product);
    if (!sizes.length) return '';
    const current = state.pickedSize[product.id];
    if (current && sizes.some((s) => s === current)) return current;
    return sizes[0];
  }

  function sizeTagsHtml(product) {
    const sizes = productSizes(product);
    if (!sizes.length) return '';
    const selected = pickedSize(product);
    return `<div class="color-tags">${sizes.map((s) => `
      <button type="button" class="color-tag${s === selected ? ' is-on' : ''}" data-size-pick="${product.id}" data-size="${escapeHtml(s)}">${escapeHtml(s)}</button>
    `).join('')}</div>`;
  }

  function sameVariant(item, id, color, size) {
    return item.id === id && (item.color || '') === (color || '') && (item.size || '') === (size || '');
  }

  function cartLine(product) {
    const color = pickedColor(product);
    const size = pickedSize(product);
    return state.cart.find((i) => sameVariant(i, product.id, color, size)) || null;
  }

  function cartQty(product) {
    return cartLine(product)?.qty || 0;
  }

  function cartQtyAll(product) {
    return state.cart.filter((i) => i.id === product.id).reduce((n, i) => n + i.qty, 0);
  }

  function qtyBarHtml(product) {
    const qty = cartQty(product);
    if (!qty) return '';
    const color = pickedColor(product);
    const size = pickedSize(product);
    const left = qty > 1
      ? `<button type="button" data-qty="${product.id}" data-color="${escapeHtml(color)}" data-size="${escapeHtml(size)}" data-delta="-1" aria-label="Restar">−</button>`
      : `<button type="button" data-remove="${product.id}" data-color="${escapeHtml(color)}" data-size="${escapeHtml(size)}" aria-label="Quitar">
          <span class="material-symbols-outlined">delete</span>
        </button>`;
    return `
      <div class="card-qty-box">
        <div class="card-qty">
          ${left}
          <span>${qty}</span>
          <button type="button" data-add="${product.id}" aria-label="Agregar uno más">+</button>
        </div>
        <div class="card-qty-steps">
          <button type="button" data-add="${product.id}" data-add-n="10">+10</button>
          <button type="button" data-add="${product.id}" data-add-n="24">+24</button>
          <button type="button" data-add="${product.id}" data-add-n="50">+50</button>
        </div>
        <button class="card-qty-clear" type="button" data-remove="${product.id}" data-color="${escapeHtml(color)}" data-size="${escapeHtml(size)}">
          <span class="material-symbols-outlined">delete</span>
          Eliminar todos
        </button>
      </div>
    `;
  }

  function renderGrid() {
    const list = products.filter(matches);
    if (els.status) {
      els.status.textContent = state.categoryId || state.q
        ? `Mostrando ${list.length} producto${list.length === 1 ? '' : 's'}.`
        : 'Mostrando todas las categorías.';
    }
    if (!list.length) {
      els.grid.innerHTML = '<div class="empty-cat">No hay productos con ese filtro.</div>';
      return;
    }
    els.grid.innerHTML = list.map((p) => {
      const qty = cartQty(p);
      const qtyAll = cartQtyAll(p);
      return `
      <article class="card${qty ? ' is-in' : ''}" data-add-once="${p.id}">
        <div class="card-img">
          ${p.image ? `<img src="${p.image}" alt="">` : '<span class="material-symbols-outlined" style="font-size:42px;color:#a8a29a">inventory_2</span>'}
          ${qtyAll ? `<span class="card-badge">${qtyAll}</span>` : ''}
          <button class="card-quick-add" type="button" data-add-once="${p.id}" aria-label="Agregar a cotización">
            <span class="material-symbols-outlined">add_shopping_cart</span>
          </button>
        </div>
        <div class="card-body">
          <h3>${escapeHtml(p.name)}</h3>
          ${p.sku ? `<div class="sku">Ref. ${escapeHtml(p.sku)}</div>` : ''}
          ${p.compatible ? `<div class="fit">${escapeHtml(p.compatible)}</div>` : ''}
          ${colorTagsHtml(p)}
          ${sizeTagsHtml(p)}
          ${showPrices && p.price != null ? `<div class="price">${money(p.price)}</div>` : ''}
          ${qtyBarHtml(p)}
        </div>
      </article>
    `;
    }).join('');
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function addToCart(id, color, amount, once, size) {
    const product = products.find((p) => p.id === id);
    if (!product) return;
    const colors = productColors(product);
    let use = '';
    if (colors.length) {
      use = color && colors.includes(color) ? color : pickedColor(product);
      state.picked[id] = use;
    }
    const sizes = productSizes(product);
    let useSize = '';
    if (sizes.length) {
      useSize = size && sizes.includes(size) ? size : pickedSize(product);
      state.pickedSize[id] = useSize;
    }
    const row = state.cart.find((i) => sameVariant(i, id, use, useSize));
    if (row && once) return;
    const extra = Math.max(1, Math.round(Number(amount) || 1));
    if (row) row.qty = Math.min(9999, row.qty + extra);
    else state.cart.push({ id, qty: Math.min(9999, extra), color: use, size: useSize });
    saveCart();
    renderCart();
    renderGrid();
  }

  function setQty(id, qty, color, size) {
    const key = color || '';
    const sizeKey = size || '';
    if (qty < 1) {
      state.cart = state.cart.filter((i) => !sameVariant(i, id, key, sizeKey));
    } else {
      const row = state.cart.find((i) => sameVariant(i, id, key, sizeKey));
      if (row) row.qty = qty;
    }
    saveCart();
    renderCart();
    renderGrid();
  }

  function pruneCart() {
    const next = state.cart.filter((item) => products.some((product) => product.id === item.id));
    if (next.length !== state.cart.length) {
      state.cart = next;
      saveCart();
    }
  }

  function showEmptyQuote() {
    if (els.emptyQuote) els.emptyQuote.style.display = 'flex';
    if (els.quoteList) els.quoteList.innerHTML = '';
    if (els.wa) els.wa.disabled = true;
    if (els.quoteTotal) els.quoteTotal.hidden = true;
    if (els.quoteTotalValue) els.quoteTotalValue.textContent = '';
    if (els.quoteRetailRow) els.quoteRetailRow.hidden = true;
    if (els.quoteMin) {
      els.quoteMin.hidden = true;
      els.quoteMin.textContent = '';
    }
    if (els.drawerFoot) els.drawerFoot.hidden = true;
    updateQuoteBtn(0, 0, false);
  }

  function updateQuoteBtn(count, total, hasPrice) {
    if (els.count) {
      els.count.textContent = String(count);
      els.count.classList.toggle('show', count > 0);
    }
    if (els.quoteBtnTotal) {
      const show = count > 0 && hasPrice;
      els.quoteBtnTotal.hidden = !show;
      els.quoteBtnTotal.textContent = show ? money(total) : '';
    }
  }

  function renderCart() {
    pruneCart();
    const count = state.cart.reduce((n, i) => n + i.qty, 0);

    if (!state.cart.length) {
      showEmptyQuote();
      return;
    }

    if (els.emptyQuote) els.emptyQuote.style.display = 'none';
    if (els.wa) els.wa.disabled = false;
    if (els.drawerFoot) els.drawerFoot.hidden = false;

    const forceWholesale = cartWholesaleByTotal();
    const detalTotal = retailCartTotal();
    let total = 0;
    let hasPrice = false;
    els.quoteList.innerHTML = state.cart.map((item) => {
      const p = products.find((x) => x.id === item.id);
      if (!p) return '';
      const retail = p.price == null ? null : Number(p.price);
      const unit = unitPrice(p, item.qty, forceWholesale);
      const line = unit == null ? null : unit * item.qty;
      if (line != null) {
        total += line;
        hasPrice = true;
      }
      const showOld = retail != null && unit != null && retail !== unit;
      const variant = [item.color, item.size].filter(Boolean).join(' · ');
      return `
        <div class="quote-item">
          ${p.image ? `<img src="${p.image}" alt="">` : '<div></div>'}
          <div>
            <strong>${escapeHtml(p.name)}</strong>
            ${variant ? `<div class="sku">${escapeHtml(variant)}</div>` : ''}
            ${p.sku ? `<div class="sku">Ref. ${escapeHtml(p.sku)}</div>` : ''}
            <div class="quote-price">
              ${showOld ? `<span class="quote-price-old">${money(retail)}</span>` : ''}
              ${unit == null ? 'Precio a confirmar' : money(unit)}
            </div>
            <div class="qty">
              <button type="button" data-qty="${p.id}" data-color="${escapeHtml(item.color || '')}" data-size="${escapeHtml(item.size || '')}" data-delta="-1">−</button>
              <span>${item.qty}</span>
              <button type="button" data-qty="${p.id}" data-color="${escapeHtml(item.color || '')}" data-size="${escapeHtml(item.size || '')}" data-delta="1">+</button>
            </div>
          </div>
          <div class="quote-line">
            ${line == null ? '' : `<div class="quote-sub">${money(line)}</div>`}
            <button class="icon-btn" type="button" data-remove="${p.id}" data-color="${escapeHtml(item.color || '')}" data-size="${escapeHtml(item.size || '')}" aria-label="Quitar">
              <span class="material-symbols-outlined">delete</span>
            </button>
          </div>
        </div>
      `;
    }).join('');

    if (els.quoteTotal && els.quoteTotalValue) {
      els.quoteTotal.hidden = !hasPrice;
      els.quoteTotalValue.textContent = money(total);
    }
    if (els.quoteRetailRow && els.quoteRetailValue) {
      const showDetal = hasPrice && detalTotal > total;
      els.quoteRetailRow.hidden = !showDetal;
      els.quoteRetailValue.textContent = money(detalTotal);
      if (els.quotePayLabel) {
        els.quotePayLabel.textContent = showDetal ? 'Total con descuento' : 'Total a pagar';
      }
    }
    if (els.quoteMin) {
      const min = Number(business.wholesale_min_total || 0);
      const missing = min - detalTotal;
      const showMin = min > 0 && missing > 0;
      els.quoteMin.hidden = !showMin;
      els.quoteMin.textContent = showMin
        ? `¡Te faltan solo $${Math.round(missing).toLocaleString('es-CO')} para obtener tu descuento!`
        : '';
    }
    updateQuoteBtn(count, detalTotal > 0 ? detalTotal : total, hasPrice);
  }

  function clearCart() {
    state.cart = [];
    saveCart();
    renderCart();
    renderGrid();
  }

  function openDrawer() {
    els.overlay.classList.add('show');
    els.drawer.classList.add('open');
  }

  function closeDrawer() {
    els.drawer.classList.remove('open');
    if (!anyPanelOpen()) els.overlay.classList.remove('show');
  }

  function openProduct(id) {
    const p = products.find((x) => x.id === id);
    if (!p) return;
    els.modal.querySelector('[data-modal-img]').innerHTML = p.image
      ? `<img src="${p.image}" alt="">`
      : '';
    els.modal.querySelector('[data-modal-title]').textContent = p.name;
    els.modal.querySelector('[data-modal-meta]').textContent = [p.sku && `Ref. ${p.sku}`, p.compatible].filter(Boolean).join(' · ');
    els.modal.querySelector('[data-modal-desc]').textContent = p.description || '';
    const colorsBox = els.modal.querySelector('[data-modal-colors]');
    if (colorsBox) colorsBox.innerHTML = colorTagsHtml(p);
    const sizesBox = els.modal.querySelector('[data-modal-sizes]');
    if (sizesBox) sizesBox.innerHTML = sizeTagsHtml(p);
    els.modal.querySelector('[data-modal-add]').dataset.add = String(p.id);
    els.modal.classList.add('show');
    els.overlay.classList.add('show');
  }

  function closeModal() {
    els.modal.classList.remove('show');
    if (!anyPanelOpen()) els.overlay.classList.remove('show');
  }

  function anyPanelOpen() {
    return els.drawer.classList.contains('open')
      || els.modal.classList.contains('show')
      || els.done?.classList.contains('show');
  }

  function openDone() {
    if (!els.done) return;
    els.done.classList.add('show');
    els.overlay.classList.add('show');
  }

  function closeDone() {
    els.done?.classList.remove('show');
    if (!anyPanelOpen()) els.overlay.classList.remove('show');
  }

  function sendWhatsApp() {
    if (!state.cart.length) return;
    const quoteUrl = root.dataset.quoteUrl;
    const csrfName = root.dataset.csrfName;
    const csrfHash = root.dataset.csrfHash;
    const csrfHeader = root.dataset.csrfHeader || 'X-CSRF-TOKEN';
    const original = els.wa.textContent;
    els.wa.disabled = true;
    els.wa.textContent = 'Generando cotización…';

    fetch(quoteUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        [csrfHeader]: csrfHash,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        [csrfName]: csrfHash,
        items: state.cart.map((item) => ({ id: item.id, qty: item.qty, color: item.color || '', size: item.size || '' })),
      }),
    })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok || !data.text) {
          throw new Error(data.error || 'No se pudo crear la cotización.');
        }
        window.open(`https://wa.me/${advisor.phone}?text=${encodeURIComponent(data.text)}`, '_blank');
        state.cart = [];
        saveCart();
        renderCart();
        renderGrid();
        closeDrawer();
        openDone();
      })
      .catch((err) => {
        alert(err.message || 'No se pudo crear la cotización.');
      })
      .finally(() => {
        els.wa.disabled = !state.cart.length;
        els.wa.textContent = original;
      });
  }

  els.search?.addEventListener('input', (e) => {
    state.q = e.target.value;
    renderGrid();
  });

  document.addEventListener('click', (e) => {
    const cat = e.target.closest('[data-cat-id]');
    if (cat) {
      state.categoryId = Number(cat.dataset.catId);
      els.cats.forEach((el) => el.classList.toggle('active', Number(el.dataset.catId) === state.categoryId));
      renderGrid();
    }
    const pick = e.target.closest('[data-color-pick]');
    if (pick) {
      const id = Number(pick.dataset.colorPick);
      state.picked[id] = pick.dataset.color || '';
      renderGrid();
      if (els.modal?.classList.contains('show')) {
        const p = products.find((x) => x.id === id);
        const box = els.modal.querySelector('[data-modal-colors]');
        if (p && box) box.innerHTML = colorTagsHtml(p);
      }
      return;
    }
    const pickSize = e.target.closest('[data-size-pick]');
    if (pickSize) {
      const id = Number(pickSize.dataset.sizePick);
      state.pickedSize[id] = pickSize.dataset.size || '';
      renderGrid();
      if (els.modal?.classList.contains('show')) {
        const p = products.find((x) => x.id === id);
        const box = els.modal.querySelector('[data-modal-sizes]');
        if (p && box) box.innerHTML = sizeTagsHtml(p);
      }
      return;
    }
    const add = e.target.closest('[data-add]');
    const remove = e.target.closest('[data-remove]');
    const qty = e.target.closest('[data-qty]');
    if (remove) {
      setQty(Number(remove.dataset.remove), 0, remove.dataset.color || '', remove.dataset.size || '');
      return;
    }
    if (qty) {
      const id = Number(qty.dataset.qty);
      const color = qty.dataset.color || '';
      const size = qty.dataset.size || '';
      const row = state.cart.find((i) => sameVariant(i, id, color, size));
      setQty(id, (row?.qty || 0) + Number(qty.dataset.delta), color, size);
      return;
    }
    if (add) {
      addToCart(Number(add.dataset.add), undefined, Number(add.dataset.addN || 1));
      return;
    }
    const addOnce = e.target.closest('[data-add-once]');
    if (addOnce) {
      if (e.target.closest('.color-tags, .card-qty-box')) return;
      addToCart(Number(addOnce.dataset.addOnce), undefined, 1, true);
      return;
    }
    const open = e.target.closest('[data-open]');
    if (open) openProduct(Number(open.dataset.open));
    if (e.target.closest('[data-open-cart]')) openDrawer();
    if (e.target.closest('[data-add-more]')) closeDrawer();
    if (e.target.closest('[data-clear-cart]')) clearCart();
    if (e.target.closest('[data-close-cart]')) closeDrawer();
    if (e.target.closest('[data-close-modal]')) closeModal();
    if (e.target.closest('[data-close-done]')) closeDone();
    if (e.target === els.overlay) {
      closeDrawer();
      closeModal();
      closeDone();
    }
    if (e.target === els.modal) closeModal();
    if (e.target === els.done) closeDone();
  });

  els.wa?.addEventListener('click', sendWhatsApp);

  renderGrid();
  renderCart();
})();
