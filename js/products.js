/* products.js — product data + rendering (home, shop, product pages) */

const PRODUCTS = [
  { id: 'p1', sku: 'DG-CLS-10', name: 'Classic Pizza Dough Balls (10 pcs)', category: 'dough', price: 450, unit: 'pack of 10 x 250g',
    rating: 5, reviews: 48, wholesale: true, featured: true, suited: ['home', 'commercial', 'bulk'], tags: ['Best Seller', 'Bulk Friendly'],
    desc: 'Our signature hand-folded dough, cold-fermented for 24 hours. Stretches easily and bakes up airy with a crisp edge.',
    specs: [['Pack size', '10 dough balls, 250g each'], ['Ingredients', 'Flour, water, salt, yeast, olive oil'], ['Storage', 'Keep chilled and use within 5 days, or freeze for up to 30 days']] },
  { id: 'p2', sku: 'DG-WHW-10', name: 'Whole Wheat Dough Balls (10 pcs)', category: 'dough', price: 520, unit: 'pack of 10 x 250g',
    rating: 4, reviews: 21, wholesale: false, featured: false, suited: ['home', 'commercial'], tags: ['Healthy Option'],
    desc: 'Nutty, wholesome dough for customers who want a healthier slice without losing the chew.',
    specs: [['Pack size', '10 dough balls, 250g each'], ['Ingredients', 'Whole wheat flour, bread flour, water, salt, yeast'], ['Storage', 'Keep chilled and use within 5 days, or freeze for up to 30 days']] },
  { id: 'p3', sku: 'DG-GHB-10', name: 'Garlic Herb Dough Balls (10 pcs)', category: 'dough', price: 550, unit: 'pack of 10 x 250g',
    rating: 5, reviews: 17, wholesale: false, featured: false, suited: ['home', 'commercial'], tags: ['New'],
    desc: 'Infused with roasted garlic and Italian herbs. A flavorful base that makes any topping stand out.',
    specs: [['Pack size', '10 dough balls, 250g each'], ['Ingredients', 'Flour, water, roasted garlic, Italian herbs, salt, yeast'], ['Storage', 'Keep chilled and use within 5 days, or freeze for up to 30 days']] },
  { id: 'p4', sku: 'BS-PB10-10', name: 'Pre-Baked Pizza Base 10"', category: 'bases', price: 380, unit: 'pack of 10',
    rating: 4, reviews: 26, wholesale: false, featured: false, suited: ['home', 'bulk'], tags: ['Quick Prep'],
    desc: 'Par-baked 10-inch bases. Just add sauce, cheese and toppings, then bake for 8 minutes.',
    specs: [['Pack size', '10 bases, 10-inch diameter'], ['Ingredients', 'Wheat flour, water, yeast, salt, olive oil'], ['Preparation', 'Top and bake about 8 minutes at 230°C']] },
  { id: 'p5', sku: 'BS-PB12-10', name: 'Pre-Baked Pizza Base 12"', category: 'bases', price: 480, unit: 'pack of 10',
    rating: 5, reviews: 39, wholesale: false, featured: true, suited: ['home', 'commercial', 'bulk'], tags: ['Best Seller', 'Quick Prep'],
    desc: 'Our most popular size for selling. Sturdy enough for heavy toppings and consistent every time.',
    specs: [['Pack size', '10 bases, 12-inch diameter'], ['Ingredients', 'Wheat flour, water, yeast, salt, olive oil'], ['Preparation', 'Top and bake about 8 minutes at 230°C']] },
  { id: 'p6', sku: 'PK-BX10-50', name: 'Pizza Box 10" (50 pcs)', category: 'boxes', price: 650, unit: 'pack of 50',
    rating: 4, reviews: 33, wholesale: true, featured: false, suited: ['home', 'commercial', 'bulk'], tags: ['Bulk Friendly'],
    desc: 'Corrugated, grease-resistant boxes with a sturdy lock-corner fold. Plain kraft, ready for your sticker.',
    specs: [['Pack size', '50 boxes'], ['Material', 'Corrugated kraft board, grease resistant'], ['Dimensions', '10 x 10 x 1.75 inches']] },
  { id: 'p7', sku: 'PK-BX12-50', name: 'Pizza Box 12" (50 pcs)', category: 'boxes', price: 780, unit: 'pack of 50',
    rating: 5, reviews: 52, wholesale: true, featured: true, suited: ['home', 'commercial', 'bulk'], tags: ['Best Seller', 'Bulk Friendly'],
    desc: 'Heat-retaining 12-inch boxes that keep pizza hot and crisp during delivery. Easy to brand.',
    specs: [['Pack size', '50 boxes'], ['Material', 'Corrugated kraft board, grease resistant'], ['Dimensions', '12 x 12 x 1.75 inches']] },
  { id: 'p8', sku: 'EQ-SLC-01', name: 'Stainless Pizza Slicer Wheel', category: 'equipment', price: 350, unit: 'each',
    rating: 5, reviews: 44, wholesale: false, featured: true, suited: ['home', 'commercial'], tags: ['Essential'],
    desc: 'Sharp 4-inch stainless steel wheel with a comfortable non-slip handle. Cuts clean through any crust.',
    specs: [['Blade', '4-inch stainless steel'], ['Handle', 'Non-slip polypropylene grip'], ['Care', 'Hand wash and dry after use']] },
  { id: 'p9', sku: 'EQ-PDL-12', name: 'Wooden Pizza Paddle (Peel)', category: 'equipment', price: 890, unit: 'each',
    rating: 5, reviews: 36, wholesale: false, featured: false, suited: ['home', 'commercial'], tags: ['Essential'],
    desc: 'Smooth hardwood peel for sliding pizzas in and out of the oven. 12-inch head, 14-inch handle.',
    specs: [['Head size', '12 inches'], ['Handle length', '14 inches'], ['Material', 'Smooth sanded hardwood']] },
  { id: 'p10', sku: 'EQ-PLT-12', name: 'Pizza Serving Plates (Set of 12)', category: 'equipment', price: 480, unit: 'set of 12',
    rating: 4, reviews: 19, wholesale: false, featured: false, suited: ['commercial', 'bulk'], tags: ['Stall Ready'],
    desc: 'Durable melamine 10-inch plates, perfect for stalls, events and dine-in tables.',
    specs: [['Set size', '12 plates'], ['Diameter', '10 inches'], ['Material', 'Durable melamine, dishwasher safe']] },
  { id: 'p11', sku: 'RM-PEP-12', name: 'Signature Pepperoni Pizza 12"', category: 'ready', price: 399, unit: 'each',
    rating: 5, reviews: 61, wholesale: false, featured: true, suited: ['home', 'bulk'], tags: ['Ready To Eat', 'Best Seller'],
    desc: 'Ready-made and freshly baked: tomato sauce, mozzarella and crispy-edged pepperoni.',
    specs: [['Size', '12-inch pizza'], ['Ingredients', 'House dough, tomato sauce, mozzarella, pepperoni'], ['Serving', 'Best eaten the same day; reheat 8 to 10 minutes at 180°C']] },
  { id: 'p12', sku: 'RM-HAW-12', name: "Danielle's Hawaiian Pizza 12\"", category: 'ready', price: 389, unit: 'each',
    rating: 4, reviews: 29, wholesale: false, featured: false, suited: ['home', 'bulk'], tags: ['Ready To Eat'],
    desc: 'Sweet pineapple, savory ham and creamy mozzarella on our house dough. Ready to eat.',
    specs: [['Size', '12-inch pizza'], ['Ingredients', 'House dough, tomato sauce, mozzarella, ham, pineapple'], ['Serving', 'Best eaten the same day; reheat 8 to 10 minutes at 180°C']] }
];

const CATEGORIES = [
  { key: 'dough', label: 'Pizza Dough', badge: 'DOUGH' },
  { key: 'bases', label: 'Bases & Crusts', badge: 'BASES' },
  { key: 'boxes', label: 'Packaging', badge: 'PACKAGING' },
  { key: 'equipment', label: 'Equipment', badge: 'EQUIPMENT' },
  { key: 'ready', label: 'Ready-Made Pizza', badge: 'PIZZA' }
];

const SUITED_LABELS = { home: 'Home Baking', commercial: 'Commercial Pizzeria', bulk: 'Bulk Events' };

const CAT_ICONS = {
  dough: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 14c-1-4 2-8 7-8s8 3 7 8c-.5 3-3.500 4-7 4s-6.500-1-7-4z"/><circle cx="10" cy="12" r=".6"/><circle cx="14" cy="13" r=".6"/></svg>',
  bases: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.500"/></svg>',
  boxes: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/></svg>',
  equipment: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="6" cy="18" r="2.500"/><circle cx="18" cy="6" r="2.500"/><path d="M8 16l8-8"/></svg>',
  ready: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 21L4 6c5-3 11-3 16 0l-8 15z"/><circle cx="11" cy="9" r="1"/><circle cx="13.500" cy="13" r="1"/></svg>'
};

function getProduct(id) { return PRODUCTS.find(p => p.id === id); }
function getCategory(key) { return CATEGORIES.find(c => c.key === key); }
function categoryLabel(key) { const c = getCategory(key); return c ? c.label : key; }
function peso(n) { return '₱' + Number(n).toLocaleString('en-PH'); }
function starString(r) { return '★'.repeat(r) + '☆'.repeat(5 - r); }
function categoryCount(key) { return PRODUCTS.filter(p => p.category === key).length; }

function productCardHTML(p) {
  return `
    <article class="product-card">
      <a href="product.html?id=${p.id}" class="product-image ph" aria-label="${p.name}">
        ${p.wholesale ? '<span class="img-badge">WHOLESALE PACK</span>' : ''}
        <span>[PRODUCT IMAGE]</span>
      </a>
      <div class="product-info">
        <span class="product-cat">${categoryLabel(p.category)}</span>
        <h3><a href="product.html?id=${p.id}" title="${p.name}">${p.name}</a></h3>
        <strong class="price">${peso(p.price)}</strong>
      </div>
      <button class="add-row" data-add="${p.id}" aria-label="Add ${p.name} to order"><span>Add to Order</span><span class="plus" aria-hidden="true">+</span></button>
    </article>`;
}

/* ---------- Home ---------- */
function renderHome() {
  const cats = document.getElementById('category-grid');
  if (cats) cats.innerHTML = CATEGORIES.map(c => `
    <a class="category-card" href="shop.html?category=${c.key}">
      <div class="cat-icon">${CAT_ICONS[c.key]}</div>
      <h3>${c.label}</h3>
      <span>${categoryCount(c.key)} Items</span>
    </a>`).join('');

  const track = document.getElementById('featured-products');
  if (track) {
    track.innerHTML = PRODUCTS.filter(p => p.featured).map(productCardHTML).join('');
    const step = () => { const card = track.querySelector('.product-card'); return card ? card.offsetWidth + 19 : 280; };
    const prev = document.getElementById('feat-prev'), next = document.getElementById('feat-next');
    if (prev) prev.onclick = () => track.scrollBy({ left: -step(), behavior: 'smooth' });
    if (next) next.onclick = () => track.scrollBy({ left: step(), behavior: 'smooth' });
  }
}

/* ---------- Shop: filters, sort, pagination ---------- */
const PAGE_SIZE = 9;

function renderShop() {
  const grid = document.getElementById('shop-grid');
  if (!grid) return;

  const params = new URLSearchParams(location.search);
  const state = { cats: new Set(), suited: new Set(), min: null, max: null, sort: 'best', page: 1, q: (params.get('q') || '').trim().toLowerCase() };
  if (params.get('category') && getCategory(params.get('category'))) state.cats.add(params.get('category'));

  const form = document.getElementById('filter-form');
  const catBox = document.getElementById('filter-cats');
  const sortSel = document.getElementById('sort-select');
  const info = document.getElementById('results-info');
  const pager = document.getElementById('pagination');
  const sub = document.getElementById('shop-sub');

  catBox.innerHTML = CATEGORIES.map(c => `
    <label class="check-row"><input type="checkbox" name="cat" value="${c.key}" ${state.cats.has(c.key) ? 'checked' : ''}>
    <span>${c.label}</span><span class="count">${categoryCount(c.key)}</span></label>`).join('');

  if (state.q) sub.textContent = 'Search results for "' + params.get('q').trim() + '"';

  function filtered() {
    let list = PRODUCTS.filter(p => {
      if (state.cats.size && !state.cats.has(p.category)) return false;
      if (state.suited.size && !p.suited.some(s => state.suited.has(s))) return false;
      if (state.min !== null && p.price < state.min) return false;
      if (state.max !== null && p.price > state.max) return false;
      if (state.q && !(p.name + ' ' + p.desc + ' ' + categoryLabel(p.category)).toLowerCase().includes(state.q)) return false;
      return true;
    });
    if (state.sort === 'best') list = [...list].sort((a, b) => b.reviews - a.reviews);
    if (state.sort === 'low') list = [...list].sort((a, b) => a.price - b.price);
    if (state.sort === 'high') list = [...list].sort((a, b) => b.price - a.price);
    if (state.sort === 'name') list = [...list].sort((a, b) => a.name.localeCompare(b.name));
    return list;
  }

  function draw() {
    const list = filtered();
    const pages = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
    if (state.page > pages) state.page = pages;
    const slice = list.slice((state.page - 1) * PAGE_SIZE, state.page * PAGE_SIZE);

    grid.innerHTML = slice.length ? slice.map(productCardHTML).join('')
      : '<div class="no-results"><h3>No products match your filters</h3><p>Try clearing a filter or widening the price range.</p></div>';
    info.innerHTML = `Showing <strong>${slice.length}</strong> of ${list.length} pizza item${list.length === 1 ? '' : 's'}`;

    if (pages <= 1) { pager.innerHTML = ''; return; }
    let html = `<button class="page-btn" data-page="${state.page - 1}" ${state.page === 1 ? 'disabled' : ''} aria-label="Previous page">←</button>`;
    for (let i = 1; i <= pages; i++) html += `<button class="page-btn ${i === state.page ? 'active' : ''}" data-page="${i}">${i}</button>`;
    html += `<button class="page-btn" data-page="${state.page + 1}" ${state.page === pages ? 'disabled' : ''} aria-label="Next page">→</button>`;
    pager.innerHTML = html;
  }

  function readForm() {
    state.cats = new Set([...form.querySelectorAll('input[name=cat]:checked')].map(i => i.value));
    state.suited = new Set([...form.querySelectorAll('input[name=suit]:checked')].map(i => i.value));
    const mn = parseFloat(form.elements['min'].value), mx = parseFloat(form.elements['max'].value);
    state.min = isNaN(mn) ? null : mn;
    state.max = isNaN(mx) ? null : mx;
    state.page = 1;
    draw();
  }

  form.addEventListener('change', readForm);
  form.addEventListener('input', e => { if (e.target.type === 'number') readForm(); });
  form.addEventListener('submit', e => e.preventDefault());
  document.getElementById('clear-filters').onclick = () => { form.reset(); state.q = ''; sub.textContent = 'Premium bases, bulk packaging, high-temp gear, and fresh preps.'; readForm(); };
  sortSel.addEventListener('change', () => { state.sort = sortSel.value; state.page = 1; draw(); });
  pager.addEventListener('click', e => {
    const b = e.target.closest('[data-page]');
    if (!b || b.disabled) return;
    state.page = parseInt(b.dataset.page, 10);
    draw();
    document.getElementById('results-bar').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  const toggle = document.getElementById('filter-toggle');
  if (toggle) toggle.onclick = () => document.getElementById('filter-card').classList.toggle('open');

  draw();
}

/* ---------- Product details ---------- */
function renderProductPage() {
  const box = document.getElementById('product-detail');
  if (!box) return;
  const p = getProduct(new URLSearchParams(location.search).get('id'));
  if (!p) {
    box.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><h2>Product not found</h2><p>That item does not exist.</p><a class="btn" href="shop.html">Back to Shop</a></div>';
    document.getElementById('product-extra').classList.add('hidden');
    return;
  }
  document.title = p.name + " | Danielle's Crafted Pizza";
  const cat = getCategory(p.category);
  document.getElementById('breadcrumb').innerHTML =
    `<a href="index.html">Home</a><span>›</span><a href="shop.html?category=${p.category}">${cat.label}</a><span>›</span><span class="here">${p.name}</span>`;

  const badge = p.tags.includes('Best Seller') ? 'BESTSELLER ' + cat.badge : cat.label.toUpperCase();
  box.innerHTML = `
    <div>
      <div class="detail-main ph" style="border-radius:10px">[PRODUCT IMAGE]</div>
      <div class="thumbs"><div class="ph" style="border-radius:6px">[IMAGE 2]</div><div class="ph" style="border-radius:6px">[IMAGE 3]</div><div class="ph" style="border-radius:6px">[IMAGE 4]</div></div>
    </div>
    <div class="detail-card">
      <span class="tag-label">${badge}</span>
      <h1>${p.name}</h1>
      <div class="rating"><span class="stars" aria-label="${p.rating} out of 5 stars">${starString(p.rating)}</span><span>(${p.reviews} reviews)</span></div>
      <div class="detail-price">${peso(p.price)} <small>/ ${p.unit}</small></div>
      <p class="detail-desc">${p.desc}</p>
      <div class="buy-row">
        <div class="qty-control">
          <button type="button" id="qty-minus" aria-label="Decrease">−</button>
          <input id="detail-qty" type="number" min="1" max="99" value="1" aria-label="Quantity">
          <button type="button" id="qty-plus" aria-label="Increase">+</button>
        </div>
        <button class="btn" id="detail-add"></button>
      </div>
      <div class="notice"><span>Selling pizza? Orders over <b>₱3,000 ship free</b>, and wholesale pricing is available for bigger orders.</span></div>
      <div class="meta">
        <div><strong>SKU:</strong> ${p.sku}</div>
        <div><strong>Category:</strong> ${cat.label}</div>
        <div><strong>${p.specs[1][0]}:</strong> ${p.specs[1][1]}</div>
      </div>
    </div>`;

  const qty = document.getElementById('detail-qty');
  const addBtn = document.getElementById('detail-add');
  const clamp = () => { let v = parseInt(qty.value, 10); if (isNaN(v) || v < 1) v = 1; if (v > 99) v = 99; qty.value = v; return v; };
  const label = () => { addBtn.textContent = 'Add to Cart • ' + peso(p.price * clamp()); };
  document.getElementById('qty-minus').onclick = () => { qty.value = clamp() - 1; label(); };
  document.getElementById('qty-plus').onclick = () => { qty.value = clamp() + 1; label(); };
  qty.addEventListener('change', label);
  addBtn.onclick = () => addToCart(p.id, clamp());
  label();

  /* tabs */
  const panels = {
    desc: `<p>${p.desc}</p><p>Packed in seller-friendly quantities and prepared fresh for pickup or delivery. Message us for wholesale pricing or custom orders.</p>`,
    specs: `<table class="spec-table"><tbody>${p.specs.map(s => `<tr><th>${s[0]}</th><td>${s[1]}</td></tr>`).join('')}<tr><th>SKU</th><td>${p.sku}</td></tr></tbody></table>`,
    reviews: `<div class="review"><span class="stars">${starString(5)}</span> <strong>Chef Marco</strong><p>Consistent every single batch. Our customers noticed the difference right away.</p></div>
      <div class="review"><span class="stars">${starString(p.rating)}</span> <strong>Sarah L.</strong><p>Great value for the pack size and easy to reorder. Sample review for demonstration.</p></div>`
  };
  const tabsEl = document.getElementById('tabs');
  const panel = document.getElementById('tab-panel');
  tabsEl.innerHTML = `
    <button class="tab active" data-tab="desc">Description</button>
    <button class="tab" data-tab="specs">Ingredients / Specs</button>
    <button class="tab" data-tab="reviews">Customer Reviews (${p.reviews})</button>`;
  panel.innerHTML = panels.desc;
  tabsEl.addEventListener('click', e => {
    const b = e.target.closest('[data-tab]');
    if (!b) return;
    tabsEl.querySelectorAll('.tab').forEach(t => t.classList.toggle('active', t === b));
    panel.innerHTML = panels[b.dataset.tab];
  });

  const rel = document.getElementById('related-products');
  if (rel) rel.innerHTML = PRODUCTS.filter(x => x.id !== p.id && x.category !== p.category)
    .concat(PRODUCTS.filter(x => x.id !== p.id && x.category === p.category)).slice(0, 4).map(productCardHTML).join('');
}

document.addEventListener('DOMContentLoaded', () => {
  renderHome();
  renderShop();
  renderProductPage();
});
