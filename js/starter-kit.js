/* starter-kit.js — Pizza Business Starter Kit Builder */

/* Each kit: list of [productId, quantity]. Totals are computed from PRODUCTS prices. */
const STARTER_KITS = {
  home: {
    3000:  { name: 'Home Business Starter',  note: 'Enough to test your menu and take your first neighborhood orders.',
             items: [['p1', 2], ['p4', 1], ['p6', 1], ['p8', 1], ['p10', 1]] },
    5000:  { name: 'Home Business Growth',   note: 'Add flavor variety and a bigger box supply for weekend selling.',
             items: [['p1', 3], ['p3', 1], ['p4', 2], ['p6', 2], ['p8', 1], ['p10', 1]] },
    10000: { name: 'Home Business Pro',      note: 'A full setup with multiple dough types, both box sizes and a paddle.',
             items: [['p1', 4], ['p2', 2], ['p3', 2], ['p5', 2], ['p6', 2], ['p7', 1], ['p8', 1], ['p9', 1], ['p10', 1]] }
  },
  stall: {
    3000:  { name: 'Food Stall Starter',     note: 'Fast-moving basics for a small stall or pop-up table.',
             items: [['p1', 3], ['p6', 1], ['p8', 1], ['p10', 1]] },
    5000:  { name: 'Food Stall Rush',        note: 'Extra 12" boxes and dough for busy days and events.',
             items: [['p1', 4], ['p3', 1], ['p7', 2], ['p8', 1], ['p10', 1]] },
    10000: { name: 'Food Stall Max',         note: 'High volume supplies for a full day of selling plus pro tools.',
             items: [['p1', 6], ['p2', 3], ['p5', 2], ['p7', 3], ['p8', 1], ['p9', 1], ['p10', 1]] }
  },
  shop: {
    3000:  { name: 'Small Shop Starter',     note: 'Core supplies to open the doors with a limited menu.',
             items: [['p1', 3], ['p7', 1], ['p8', 1], ['p10', 1]] },
    5000:  { name: 'Small Shop Standard',    note: 'Dough, packaging and a proper paddle for daily service.',
             items: [['p1', 4], ['p2', 1], ['p7', 2], ['p9', 1]] },
    10000: { name: 'Small Shop Complete',    note: 'Everything for a full menu: three doughs, boxes and all equipment.',
             items: [['p1', 6], ['p2', 4], ['p3', 2], ['p7', 3], ['p8', 1], ['p9', 1], ['p10', 1]] }
  }
};

const BUSINESS_LABELS = { home: 'Home Business', stall: 'Food Stall', shop: 'Small Shop' };

function kitTotal(kit) {
  return kit.items.reduce((s, [id, q]) => s + getProduct(id).price * q, 0);
}

function initStarterKit() {
  const form = document.getElementById('kit-form');
  const out = document.getElementById('kit-result');
  if (!form || !out) return;

  let currentKit = null;

  form.addEventListener('submit', e => {
    e.preventDefault();
    const type = form.elements['businessType'].value;
    const budget = form.elements['budget'].value;
    if (!type || !budget) {
      out.innerHTML = '<p class="form-error">Please choose a business type and a budget.</p>';
      return;
    }
    const kit = STARTER_KITS[type][budget];
    currentKit = kit;
    const total = kitTotal(kit);
    const left = Number(budget) - total;

    const rows = kit.items.map(([id, q]) => {
      const p = getProduct(id);
      return `<tr>
        <td><a href="product.html?id=${p.id}">${p.name}</a><span class="product-cat"> ${categoryLabel(p.category)}</span></td>
        <td class="num">${q}</td>
        <td class="num">${peso(p.price * q)}</td>
      </tr>`;
    }).join('');

    out.innerHTML = `
      <div class="kit-card">
        <span class="tag">${BUSINESS_LABELS[type]} · ${peso(Number(budget))} budget</span>
        <h3>${kit.name}</h3>
        <p>${kit.note}</p>
        <div class="table-wrap">
          <table class="kit-table">
            <thead><tr><th>Item</th><th class="num">Qty</th><th class="num">Cost</th></tr></thead>
            <tbody>${rows}</tbody>
            <tfoot><tr><td colspan="2">Estimated Total</td><td class="num">${peso(total)}</td></tr></tfoot>
          </table>
        </div>
        <p class="note ok">${peso(left)} left in your budget.</p>
        <button class="btn btn-large" id="add-kit" type="button">Add Entire Kit To Cart</button>
      </div>`;

    document.getElementById('add-kit').onclick = () => {
      if (!currentKit) return;
      currentKit.items.forEach(([id, q]) => addToCart(id, q, true));
      showToast('Kit added to cart');
      const btn = document.getElementById('add-kit');
      btn.textContent = 'Added. View Cart →';
      btn.onclick = () => { location.href = 'cart.html'; };
    };
  });
}

document.addEventListener('DOMContentLoaded', initStarterKit);
