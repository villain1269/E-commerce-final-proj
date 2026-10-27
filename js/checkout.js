/* checkout.js — checkout summary, validation, order placement, confirmation */

const ORDER_KEY = 'dcp_last_order';
const SHIP_LABELS = { express: 'Express Food Dispatch', standard: 'Standard Delivery' };
const PAYMENT_LABELS = { cod: 'Cash on Delivery', gcash: 'GCash', bank: 'Bank Transfer' };

function selectedShipping(form) {
  const v = form && form.elements['shipping'] ? form.elements['shipping'].value : '';
  return v || 'express';
}

function updateShippingPrices() {
  const sub = cartSubtotal();
  const std = shippingFor(sub, 'standard');
  const expEl = document.getElementById('price-express');
  const stdEl = document.getElementById('price-standard');
  if (expEl) expEl.textContent = peso(shippingFor(sub, 'express'));
  if (stdEl) stdEl.textContent = std === 0 ? 'FREE' : peso(std);
}

function renderCheckoutSummary() {
  const box = document.getElementById('checkout-summary');
  if (!box) return;
  const form = document.getElementById('checkout-form');
  const method = selectedShipping(form);
  const cart = getCart().filter(i => getProduct(i.id));
  const sub = cartSubtotal();
  const ship = shippingFor(sub, method);
  const total = sub + ship;
  box.innerHTML = `
    <h3>Review Your Order</h3>
    ${cart.map(i => {
      const p = getProduct(i.id);
      return `<div class="co-item"><div class="cart-thumb ph">[IMG]</div>
        <div class="co-name">${p.name}<small>Qty: ${i.qty}</small></div>
        <div class="co-price">${peso(p.price * i.qty)}</div></div>`;
    }).join('')}
    <div class="co-divider"></div>
    <div class="summary-line"><span>Subtotal</span><span>${peso(sub)}</span></div>
    <div class="summary-line"><span>${SHIP_LABELS[method]}</span><span>${ship === 0 ? 'FREE' : peso(ship)}</span></div>
    <div class="summary-line total"><span>Final Total</span><span>${peso(total)}</span></div>
    <button type="submit" form="checkout-form" class="btn btn-block">Place Order • ${peso(total)}</button>`;
}

/* ---------- Validators ---------- */
const VALIDATORS = {
  firstName: v => v.trim().length >= 2 || 'Enter your first name.',
  lastName:  v => v.trim().length >= 2 || 'Enter your last name.',
  email:     v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) || 'Enter a valid email address.',
  address:   v => v.trim().length >= 8 || 'Enter your full street address.',
  city:      v => v.trim().length >= 2 || 'Enter your city or municipality.',
  postal:    v => /^\d{4}$/.test(v.trim()) || 'Postal code must be 4 digits.',
  phone:     v => /^(09|\+639)\d{9}$/.test(v.replace(/[\s-]/g, '')) || 'Enter a valid PH mobile number (e.g. 09171234567).'
};

function setError(field, msg) {
  const holder = field.closest('.field');
  if (!holder) return;
  holder.classList.toggle('invalid', !!msg);
  let err = holder.querySelector('.error-msg');
  if (!err) { err = document.createElement('span'); err.className = 'error-msg'; holder.appendChild(err); }
  err.textContent = msg || '';
}

function validateField(field) {
  const fn = VALIDATORS[field.name];
  if (!fn) return true;
  const res = fn(field.value);
  setError(field, res === true ? '' : res);
  return res === true;
}

function setupCheckout() {
  const form = document.getElementById('checkout-form');
  if (!form) return;

  if (getCart().length === 0) {
    document.getElementById('checkout-wrap').innerHTML = `
      <div class="container" style="padding:3rem 0 4rem"><div class="empty-state"><h2>Your cart is empty</h2><p>Add something before checking out.</p>
      <a class="btn" href="shop.html">Go to Shop</a></div></div>`;
    return;
  }
  updateShippingPrices();
  renderCheckoutSummary();

  form.querySelectorAll('input[name], textarea[name]').forEach(f => {
    f.addEventListener('blur', () => validateField(f));
    f.addEventListener('input', () => { if (f.closest('.field') && f.closest('.field').classList.contains('invalid')) validateField(f); });
  });
  form.querySelectorAll('input[name=shipping]').forEach(r => r.addEventListener('change', renderCheckoutSummary));

  form.addEventListener('submit', e => {
    e.preventDefault();
    let ok = true, firstBad = null;
    form.querySelectorAll('input[name], textarea[name]').forEach(f => {
      if (!validateField(f)) { ok = false; if (!firstBad) firstBad = f; }
    });

    const payment = form.elements['payment'].value;
    document.getElementById('payment-error').textContent = payment ? '' : 'Please choose a payment method.';
    if (!payment) ok = false;

    const terms = form.elements['terms'];
    document.getElementById('terms-error').textContent = terms.checked ? '' : 'Please accept the order terms.';
    if (!terms.checked) ok = false;

    if (!ok) { if (firstBad) firstBad.focus(); return; }

    const method = selectedShipping(form);
    const cart = getCart().filter(i => getProduct(i.id));
    const sub = cartSubtotal();
    const ship = shippingFor(sub, method);
    const val = n => form.elements[n].value.trim();
    const order = {
      ref: 'DCP-' + Date.now().toString().slice(-8),
      date: new Date().toISOString(),
      customer: {
        name: val('firstName') + ' ' + val('lastName'),
        email: val('email'),
        phone: val('phone'),
        address: val('address') + ', ' + val('city') + ' ' + val('postal')
      },
      notes: val('notes'),
      shippingMethod: method,
      payment,
      items: cart.map(i => { const p = getProduct(i.id); return { id: p.id, name: p.name, qty: i.qty, price: p.price }; }),
      subtotal: sub, shipping: ship, total: sub + ship
    };
    localStorage.setItem(ORDER_KEY, JSON.stringify(order));
    clearCart();
    location.href = 'confirmation.html';
  });
}

/* ---------- Confirmation page ---------- */
function renderConfirmation() {
  const box = document.getElementById('confirmation-content');
  if (!box) return;
  let order = null;
  try { order = JSON.parse(localStorage.getItem(ORDER_KEY)); } catch (e) {}

  if (!order) {
    box.innerHTML = `<div class="empty-state" style="margin:3rem 0"><h2>No recent order found</h2>
      <p>Place an order and your confirmation will appear here.</p>
      <a class="btn" href="shop.html">Go to Shop</a></div>`;
    return;
  }

  const safe = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  box.innerHTML = `
    <div class="confirm-head">
      <div class="check-badge">✓</div>
      <h1>Thank you, ${safe(order.customer.name.split(' ')[0])}!</h1>
      <p>Your order has been received. A confirmation would be sent to <strong>${safe(order.customer.email)}</strong>.</p>
      <p class="order-ref">Order No. <strong>${order.ref}</strong></p>
    </div>
    <div class="confirm-grid">
      <div class="summary-box">
        <h3>Items</h3>
        ${order.items.map(i => `<div class="summary-item"><span>${safe(i.name)} <small>× ${i.qty}</small></span><span>${peso(i.price * i.qty)}</span></div>`).join('')}
        <div class="summary-line"><span>Subtotal</span><span>${peso(order.subtotal)}</span></div>
        <div class="summary-line"><span>${SHIP_LABELS[order.shippingMethod] || 'Delivery'}</span><span>${order.shipping === 0 ? 'FREE' : peso(order.shipping)}</span></div>
        <div class="summary-line total"><span>Total</span><span>${peso(order.total)}</span></div>
      </div>
      <div class="summary-box">
        <h3>Delivery Details</h3>
        <p><strong>${safe(order.customer.name)}</strong><br>${safe(order.customer.address)}<br>${safe(order.customer.phone)}</p>
        <h3>Payment</h3>
        <p>${PAYMENT_LABELS[order.payment] || safe(order.payment)}</p>
        ${order.notes ? `<h3>Notes</h3><p>${safe(order.notes)}</p>` : ''}
      </div>
    </div>
    <div class="center-actions">
      <a class="btn" href="shop.html">Continue Shopping</a>
      <a class="btn btn-outline" href="index.html">Back to Home</a>
    </div>`;
}

document.addEventListener('DOMContentLoaded', () => { setupCheckout(); renderConfirmation(); });
