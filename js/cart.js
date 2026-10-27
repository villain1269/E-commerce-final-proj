/* cart.js — cart state (localStorage), shared header/footer, mobile menu, search, cart page */

const CART_KEY = 'dcp_cart';
const FREE_SHIPPING_MIN = 3000;
const SHIPPING_RATES = { standard: 150, express: 250 };

/* ---------- Cart state ---------- */
function getCart() {
  try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; }
  catch (e) { return []; }
}
function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount();
}
function addToCart(id, qty = 1, silent = false) {
  if (!getProduct(id)) return;
  const cart = getCart();
  const line = cart.find(i => i.id === id);
  if (line) line.qty = Math.min(99, line.qty + qty);
  else cart.push({ id, qty });
  saveCart(cart);
  if (!silent) showToast('Added to cart');
}
function updateQty(id, qty) {
  const cart = getCart();
  const line = cart.find(i => i.id === id);
  if (!line) return;
  line.qty = Math.max(1, Math.min(99, qty));
  saveCart(cart);
}
function removeItem(id) { saveCart(getCart().filter(i => i.id !== id)); }
function clearCart() { saveCart([]); }
function cartItemCount() { return getCart().reduce((n, i) => n + i.qty, 0); }
function cartSubtotal() {
  return getCart().reduce((sum, i) => {
    const p = getProduct(i.id);
    return sum + (p ? p.price * i.qty : 0);
  }, 0);
}
/* standard delivery is free over the minimum; express always costs the express rate */
function shippingFor(subtotal, method = 'standard') {
  if (subtotal === 0) return 0;
  if (method === 'express') return SHIPPING_RATES.express;
  return subtotal >= FREE_SHIPPING_MIN ? 0 : SHIPPING_RATES.standard;
}
function updateCartCount() {
  const n = cartItemCount();
  document.querySelectorAll('.cart-count').forEach(el => { el.textContent = n; });
}

/* ---------- Toast ---------- */
function showToast(msg) {
  let t = document.getElementById('toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'toast';
    t.setAttribute('role', 'status');
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(showToast._t);
  showToast._t = setTimeout(() => t.classList.remove('show'), 1800);
}

/* ---------- Shared header / footer ---------- */
const SVG = {
  search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.500-3.500"/></svg>',
  cart: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.500 11h10l2-8H6.500"/><circle cx="9" cy="19.500" r="1.200"/><circle cx="17" cy="19.500" r="1.200"/></svg>',
  lock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>'
};

function logoHTML() {
  return `<a href="index.html" class="logo" aria-label="Danielle's Crafted Pizza home">
    <span class="logo-mark">D</span>
    <span class="logo-text"><b>DANIELLE'S</b><em>CRAFTED PIZZA</em></span></a>`;
}

function buildLayout() {
  const header = document.getElementById('site-header');
  const isCheckout = document.body.dataset.layout === 'checkout';

  if (header && isCheckout) {
    header.innerHTML = `
      <header class="site-header">
        <div class="container nav-wrap nav-checkout">
          ${logoHTML()}
          <span class="secure-note">${SVG.lock} Secure Checkout</span>
          <a href="cart.html" class="return-link">Return to Cart</a>
        </div>
      </header>
      <div class="steps-bar"><div class="container">
        <span class="on">1. Shipping Info</span><span class="sep">›</span>
        <span>2. Shipping Method</span><span class="sep">›</span>
        <span>3. Payment Details</span>
      </div></div>`;
  } else if (header) {
    header.innerHTML = `
      <div class="announcement">Free delivery on orders over <b>₱3,000</b> &nbsp;|&nbsp; Wholesale pricing for pizza sellers</div>
      <header class="site-header">
        <div class="container nav-wrap">
          <button class="menu-toggle" id="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
          </button>
          ${logoHTML()}
          <nav class="main-nav" id="main-nav" aria-label="Main">
            <ul>
              <li class="has-sub">
                <a href="shop.html">Shop</a>
                <ul class="submenu">
                  <li><a href="shop.html">All Pizza Supplies</a></li>
                  <li><a href="shop.html?category=dough">Pizza Dough</a></li>
                  <li><a href="shop.html?category=bases">Bases &amp; Crusts</a></li>
                  <li><a href="shop.html?category=boxes">Packaging</a></li>
                  <li><a href="shop.html?category=equipment">Equipment</a></li>
                  <li><a href="shop.html?category=ready">Ready-Made Pizza</a></li>
                </ul>
              </li>
              <li><a href="index.html#starter-kit">Starter Kit</a></li>
              <li><a href="index.html#wholesale">Wholesale</a></li>
              <li><a href="index.html#why-us">About Us</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </nav>
          <div class="header-actions">
            <button class="icon-btn" id="search-toggle" aria-label="Search products" aria-expanded="false">${SVG.search}</button>
            <a href="cart.html" class="cart-link" aria-label="Cart">${SVG.cart} <span>Cart (<span class="cart-count">0</span>)</span></a>
          </div>
        </div>
        <div class="search-bar hidden" id="search-bar">
          <div class="container">
            <form id="search-form" role="search">
              <input type="text" id="search-input" placeholder="Search dough, boxes, paddles..." aria-label="Search products">
              <button class="btn" type="submit">Search</button>
            </form>
          </div>
        </div>
      </header>`;
  }

  const footer = document.getElementById('site-footer');
  if (footer) footer.innerHTML = `
    <footer class="site-footer" id="contact">
      <div class="container footer-grid">
        <div class="footer-brand">
          <h4>Danielle's Crafted Pizza</h4>
          <p>Premium ingredients, expert-grade equipment, and culinary resources designed for home chefs and thriving pizzerias.</p>
          <p class="tagline">Everything You Need for Pizza Success.</p>
        </div>
        <div>
          <h5>Products</h5>
          <ul>
            <li><a href="shop.html?category=dough">Pizza Dough</a></li>
            <li><a href="shop.html?category=bases">Bases &amp; Crusts</a></li>
            <li><a href="shop.html?category=boxes">Packaging</a></li>
            <li><a href="shop.html?category=equipment">Equipment</a></li>
            <li><a href="shop.html?category=ready">Ready-Made</a></li>
          </ul>
        </div>
        <div>
          <h5>Businesses</h5>
          <ul>
            <li><a href="index.html#wholesale">Wholesale Program</a></li>
            <li><a href="index.html#starter-kit">Starter Kit Builder</a></li>
            <li><a href="shop.html?category=boxes">Bulk Packaging</a></li>
            <li><a href="index.html#why-us">Why Danielle's</a></li>
          </ul>
        </div>
        <div>
          <h5>Contact</h5>
          <ul>
            <li>hello@daniellescraftedpizza.example</li>
            <li>+63 900 000 0000</li>
            <li>Mon to Sat, 8am to 6pm</li>
            <li><a href="cart.html">Your Cart</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <span>&copy; 2026 Danielle's Crafted Pizza. Student project, not a real store.</span>
          <span class="social" aria-label="Social links">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="4"/><circle cx="12" cy="12" r="3.500"/><circle cx="17" cy="7" r=".6"/></svg>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M21 5c-1 .5-2 .8-3 .9a4 4 0 00-7 3.100A11 11 0 013 4s-4 9 5 13a12 12 0 01-6 2c9 5 19 0 19-11.500 0-.3 1-1.500 1-2.500z"/></svg>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M10 8.500l5 3.500-5 3.500z"/></svg>
          </span>
        </div>
      </div>
    </footer>`;
}

function setupMenu() {
  const btn = document.getElementById('menu-toggle');
  const nav = document.getElementById('main-nav');
  if (btn && nav) {
    btn.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      btn.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open);
    });
    nav.addEventListener('click', e => {
      if (e.target.tagName === 'A') {
        nav.classList.remove('open');
        btn.classList.remove('open');
        btn.setAttribute('aria-expanded', false);
      }
    });
  }

  const sBtn = document.getElementById('search-toggle');
  const sBar = document.getElementById('search-bar');
  if (sBtn && sBar) {
    sBtn.addEventListener('click', () => {
      const show = sBar.classList.toggle('hidden') === false;
      sBtn.setAttribute('aria-expanded', show);
      if (show) document.getElementById('search-input').focus();
    });
    document.getElementById('search-form').addEventListener('submit', e => {
      e.preventDefault();
      const v = document.getElementById('search-input').value.trim();
      location.href = v ? 'shop.html?q=' + encodeURIComponent(v) : 'shop.html';
    });
  }
}

/* ---------- Cart page ---------- */
function renderCartPage() {
  const wrap = document.getElementById('cart-content');
  if (!wrap) return;
  const cart = getCart().filter(i => getProduct(i.id));

  if (cart.length === 0) {
    wrap.innerHTML = `
      <div class="empty-state">
        <h2>Your cart is empty</h2>
        <p>Add some dough, boxes or equipment to get started.</p>
        <a class="btn" href="shop.html">Start Shopping</a>
        <a class="btn btn-outline" href="index.html#starter-kit">Try the Starter Kit Builder</a>
      </div>`;
    renderUpsell();
    return;
  }

  const rows = cart.map(i => {
    const p = getProduct(i.id);
    return `
      <div class="cart-row" data-id="${p.id}">
        <div class="cart-item">
          <div class="cart-thumb ph">[IMG]</div>
          <div><a href="product.html?id=${p.id}"><strong>${p.name}</strong></a><small>SKU: ${p.sku}</small></div>
        </div>
        <span class="cart-price">${peso(p.price)}</span>
        <div class="qty-control">
          <button type="button" data-act="minus" aria-label="Decrease quantity">−</button>
          <input type="number" min="1" max="99" value="${i.qty}" data-act="input" aria-label="Quantity">
          <button type="button" data-act="plus" aria-label="Increase quantity">+</button>
        </div>
        <strong class="line-total">${peso(p.price * i.qty)}</strong>
        <button class="remove-btn" data-act="remove" aria-label="Remove ${p.name}">✕</button>
      </div>`;
  }).join('');

  const sub = cartSubtotal();
  const ship = shippingFor(sub);
  const toFree = FREE_SHIPPING_MIN - sub;

  wrap.innerHTML = `
    <div class="cart-layout">
      <div class="cart-table">
        <div class="cart-head"><span>Product</span><span>Price</span><span>Quantity</span><span>Total</span><span></span></div>
        ${rows}
      </div>
      <aside class="summary-box">
        <h3>Order Summary</h3>
        <div class="summary-line"><span>Subtotal</span><span>${peso(sub)}</span></div>
        <div class="summary-line"><span>Standard Delivery</span><span class="${ship === 0 ? '' : 'red'}">${ship === 0 ? 'FREE' : peso(ship)}</span></div>
        <div class="summary-line total"><span>Total</span><span>${peso(sub + ship)}</span></div>
        ${toFree > 0 ? `<p class="note">Add ${peso(toFree)} more for free standard delivery. Express option at checkout.</p>` : '<p class="note ok">You qualify for free standard delivery.</p>'}
        <a class="btn btn-block" href="checkout.html">Proceed to Secure Checkout</a>
        <a class="link-center" href="shop.html">— or — Continue Shopping</a>
        <a class="link-center" href="#" id="clear-cart">Clear cart</a>
      </aside>
    </div>`;

  wrap.querySelectorAll('.cart-row').forEach(row => {
    const id = row.dataset.id;
    row.addEventListener('click', e => {
      const btn = e.target.closest('[data-act]');
      if (!btn || btn.dataset.act === 'input') return;
      const current = (getCart().find(i => i.id === id) || {}).qty || 1;
      if (btn.dataset.act === 'plus') updateQty(id, current + 1);
      if (btn.dataset.act === 'minus') updateQty(id, current - 1);
      if (btn.dataset.act === 'remove') removeItem(id);
      renderCartPage();
    });
    row.querySelector('input').addEventListener('change', e => {
      updateQty(id, parseInt(e.target.value, 10) || 1);
      renderCartPage();
    });
  });
  document.getElementById('clear-cart').onclick = e => {
    e.preventDefault();
    if (confirm('Remove all items from your cart?')) { clearCart(); renderCartPage(); }
  };
  renderUpsell();
}

function renderUpsell() {
  const box = document.getElementById('cart-upsell');
  if (!box) return;
  const inCart = new Set(getCart().map(i => i.id));
  box.innerHTML = PRODUCTS.filter(p => !inCart.has(p.id)).slice(0, 4).map(productCardHTML).join('');
}

/* ---------- Global handlers ---------- */
document.addEventListener('click', e => {
  const b = e.target.closest('[data-add]');
  if (b) {
    addToCart(b.dataset.add, 1);
    if (document.getElementById('cart-content')) renderCartPage();
  }
});

document.addEventListener('submit', e => {
  if (e.target.id !== 'newsletter-form') return;
  e.preventDefault();
  const input = e.target.querySelector('input[type=email]');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim())) { showToast('Please enter a valid email'); input.focus(); return; }
  try { localStorage.setItem('dcp_newsletter', input.value.trim()); } catch (err) {}
  input.value = '';
  showToast('Thanks for subscribing!');
});

document.addEventListener('DOMContentLoaded', () => {
  buildLayout();
  setupMenu();
  updateCartCount();
  renderCartPage();
});

/* keep counter in sync across tabs */
window.addEventListener('storage', e => { if (e.key === CART_KEY) { updateCartCount(); renderCartPage(); } });
