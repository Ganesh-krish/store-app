const API = '/api';
function showToast(msg, type='success') {
  const icon = type==='success' ? 'bi-check-circle' : 'bi-x-circle';
  const bg = type==='success' ? 'text-bg-success' : 'text-bg-danger';
  const toastEl = document.createElement('div');
  toastEl.className = 'toast align-items-center '+bg+' border-0 show';
  toastEl.style.maxWidth = '320px';
  toastEl.innerHTML = '<div class="d-flex"><div class="toast-body"><i class="bi '+icon+' me-2"></i>'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
  toastEl.style.opacity = '0';
  toastEl.style.transition = 'opacity 0.3s ease';
  document.getElementById('toastContainer').appendChild(toastEl);
  requestAnimationFrame(()=>{ toastEl.style.opacity='1'; });
  setTimeout(()=>{ toastEl.style.opacity='0'; setTimeout(()=>toastEl.remove(),300); },5000);
}
function revealBilling() {
  document.getElementById('productsSection').style.display='';
  document.getElementById('bottomSection').style.display='';
  document.getElementById('generateSection').style.display='';
}
function hideBilling() {
  document.getElementById('productsSection').style.display='none';
  document.getElementById('bottomSection').style.display='none';
  document.getElementById('generateSection').style.display='none';
}
let products = [];
let rowCounter = 0;

async function loadProducts() {
  const res = await fetch(API + '/products');
  const data = await res.json();
  products = data.products || [];
}

function createRow() {
  rowCounter++;
  const tr = document.createElement('tr');
  tr.dataset.row = rowCounter;
  tr.innerHTML = `
    <td class="sno">${rowCounter}</td>
    <td>
      <select class="form-select product-select"><option value="">Select product</option></select>
    </td>
    <td><input type="number" class="form-control qty" min="1" step="1" value="1"></td>
    <td><input type="text" class="form-control price" value="0.00" readonly></td>
    <td><input type="text" class="form-control tax" value="0" readonly></td>
    <td><input type="text" class="form-control total" value="0.00" readonly></td>
    <td><button type="button" class="btn btn-danger btn-sm del"><i class="bi bi-trash"></i></button></td>
  `;
  const sel = tr.querySelector('.product-select');
  products.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.name + ' (' + p.code + ')';
    opt.dataset.price = p.price;
    opt.dataset.tax = p.tax_percentage;
    opt.dataset.stock = p.stock;
    sel.appendChild(opt);
  });
  return tr;
}

function renumber() {
  document.querySelectorAll('#productTable tbody tr').forEach((tr,i) => {
    tr.querySelector('.sno').textContent = i+1;
  });
}

function calcRow(tr) {
  const sel = tr.querySelector('.product-select');
  const opt = sel.options[sel.selectedIndex];
  const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
  const tax = opt && opt.dataset.tax ? parseFloat(opt.dataset.tax) : 0;
  const qty = parseInt(tr.querySelector('.qty').value) || 0;
  tr.querySelector('.price').value = price ? price.toFixed(2) : '';
  tr.querySelector('.tax').value = tax ? tax.toFixed(2) : '';
  const subtotal = qty * price;
  const lineTax = subtotal * tax / 100;
  const total = subtotal + lineTax;
  tr.querySelector('.total').value = total.toFixed(2);
}

function calcAll() {
  let totalQty = 0;
  let grandTotal = 0;
  const taxGroups = {};
  document.querySelectorAll('#productTable tbody tr').forEach(tr => {
    const sel = tr.querySelector('.product-select');
    const opt = sel.options[sel.selectedIndex];
    const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
    const tax = opt && opt.dataset.tax ? parseFloat(opt.dataset.tax) : 0;
    const qty = parseInt(tr.querySelector('.qty').value) || 0;
    const subtotal = qty * price;
    const lineTax = subtotal * tax / 100;
    totalQty += qty;
    grandTotal += subtotal + lineTax;
    if (opt && opt.value) {
      taxGroups[tax] = (taxGroups[tax] || 0) + lineTax;
    }
  });
  document.getElementById('totalAmount').textContent = '₹' + grandTotal.toFixed(2);
  let sub = 0;
  document.querySelectorAll('#productTable tbody tr').forEach(tr => {
    const sel = tr.querySelector('.product-select');
    const opt = sel.options[sel.selectedIndex];
    const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
    const qty = parseInt(tr.querySelector('.qty').value) || 0;
    sub += qty*price;
  });
  const bill = document.getElementById('billPreview');
  let html = '<table class="table table-sm"><tr><td>Subtotal</td><td class="text-end">₹'+sub.toFixed(2)+'</td></tr>';
  Object.keys(taxGroups).sort().forEach(t => {
    html += '<tr><td>GST @ '+t+'%</td><td class="text-end">₹'+taxGroups[t].toFixed(2)+'</td></tr>';
  });
  const totalTax = Object.values(taxGroups).reduce((a,b)=>a+b,0);
  html += '<tr><td>Total GST</td><td class="text-end">₹'+totalTax.toFixed(2)+'</td></tr>';
  html += '<tr class="fw-bold"><td>Grand Total</td><td class="text-end">₹'+grandTotal.toFixed(2)+'</td></tr>';
  html += '</table>';
  bill.innerHTML = html;
}

function ensureOneRow() {
  const tbody = document.querySelector('#productTable tbody');
  if (tbody.children.length === 0) tbody.appendChild(createRow());
}

async function loadLowStock() {
  const threshold = document.getElementById('threshold').value || 5;
  const res = await fetch(API + '/products/low-stock?threshold=' + threshold);
  const data = await res.json();
  const list = document.getElementById('lowStockList');
  if (!data.products || data.products.length === 0) {
    list.innerHTML = '<p class="text-muted">No low stock products.</p>';
    return;
  }
  list.innerHTML = data.products.map(p => `<div class="alert alert-warning py-1 mb-1"><strong>${p.name}</strong> (${p.code}) - stock: ${p.stock}</div>`).join('');
}

let acTimer;
function setupCustomer() {
  const emailInput = document.getElementById('custEmail');
  const nameInput = document.getElementById('custName');
  const suggestions = document.getElementById('emailSuggestions');
  emailInput.addEventListener('input', () => {
    hideBilling();
    clearTimeout(acTimer);
    acTimer = setTimeout(async () => {
      const q = emailInput.value.trim();
      if (q.length < 1) { suggestions.innerHTML=''; return; }
      const res = await fetch(API + '/customers?q=' + encodeURIComponent(q));
      const data = await res.json();
      suggestions.innerHTML = '';
      (data.customers||[]).forEach(c => {
        const a = document.createElement('a');
        a.className = 'list-group-item list-group-item-action';
        a.textContent = c.email;
        a.addEventListener('click', () => {
          emailInput.value = c.email;
          nameInput.value = c.name;
          suggestions.innerHTML = '';
          revealBilling();
        });
        suggestions.appendChild(a);
      });
    }, 300);
  });
  document.getElementById('addCustomerBtn').addEventListener('click', async () => {
    const email = emailInput.value.trim();
    const name = nameInput.value.trim();
    if (!email && !name) { showToast('Email and name are required.','error'); return; }
    if (!email) { showToast('Email is required.','error'); return; }
    if (!name) { showToast('Name is required.','error'); return; }
    const res = await fetch(API + '/customers', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({email,name}) });
    const data = await res.json();
    if (!res.ok) {
      showToast(data.message || 'Customer creation failed.','error');
      return;
    }
    showToast('Customer created successfully.','success');
    revealBilling();
  });
}

function setupProducts() {
  const tbody = document.querySelector('#productTable tbody');
  tbody.appendChild(createRow());
  tbody.addEventListener('change', e => {
    if (e.target.classList.contains('product-select') || e.target.classList.contains('qty')) {
      const tr = e.target.closest('tr');
      calcRow(tr);
      calcAll();
    }
  });
  tbody.addEventListener('input', e => {
    if (e.target.classList.contains('qty')) {
      const tr = e.target.closest('tr');
      calcRow(tr);
      calcAll();
    }
  });
  tbody.addEventListener('click', e => {
    if (e.target.classList.contains('del')) {
      const tr = e.target.closest('tr');
      tr.remove();
      ensureOneRow();
      renumber();
      calcAll();
    }
  });
  document.getElementById('addProductBtn').addEventListener('click', () => {
    tbody.appendChild(createRow());
    renumber();
  });
  tbody.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') {
      const tr = e.target.closest('tr');
      if (!tr) return;
      const inputs = Array.from(tr.querySelectorAll('input,select'));
      if (e.target === inputs[inputs.length-2]) {
        tbody.appendChild(createRow());
        renumber();
      }
    }
  });
}

async function submitOrder() {
  const btn = document.getElementById('generateBtn');
  btn.disabled = true;
  const email = document.getElementById('custEmail').value.trim();
  const name = document.getElementById('custName').value.trim();
  const items = [];
  let ok = true;
  const alertBox = document.getElementById('orderAlert');
  alertBox.innerHTML = '';
  document.querySelectorAll('#productTable tbody tr').forEach(tr => {
    const sel = tr.querySelector('.product-select');
    const pid = sel.value;
    const qty = tr.querySelector('.qty').value;
    if (!pid) { ok=false; return; }
    if (!/^[1-9][0-9]*$/.test(qty)) { ok=false; return; }
    items.push({ product_id: parseInt(pid), quantity: parseInt(qty) });
  });
  if (!email || !name) { showToast('Customer email and name required.','error'); btn.disabled=false; return; }
  if (!ok || items.length===0) { showToast('All rows must have valid product and quantity.','error'); btn.disabled=false; return; }
  const ids = items.map(i=>i.product_id);
  if (new Set(ids).size !== ids.length) { showToast('Duplicate products not allowed.','error'); btn.disabled=false; return; }
  try {
    const res = await fetch(API + '/orders', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ customer:{name,email}, items }) });
    const data = await res.json();
    if (!res.ok) {
      showToast(data.message || 'Order creation failed.','error');
      btn.disabled = false;
      return;
    }
    const orderNo = data.data.id;
    const grand = data.data.grand_total;
    showToast('Order #' + orderNo + ' created. Grand total ₹' + grand, 'success');
    // reset
    document.getElementById('custEmail').value = '';
    document.getElementById('custName').value = '';
    hideBilling();
    document.querySelector('#productTable tbody').innerHTML = '';
    ensureOneRow();
    renumber();
    calcAll();
    document.getElementById('custEmail').focus();
  } catch(e) {
    showToast('Server error.','error');
  }
  btn.disabled = false;
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadProducts();
  setupCustomer();
  setupProducts();
  calcAll();
  loadLowStock();
  document.getElementById('generateBtn').addEventListener('click', submitOrder);
  document.getElementById('refreshLowStock').addEventListener('click', loadLowStock);
});
// Order history
let historyTimer;
document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('historySearch');
  if (!search) return;
  search.addEventListener('input', () => {
    clearTimeout(historyTimer);
    historyTimer = setTimeout(async () => {
      const q = search.value.trim();
      if (q.length < 1) { document.getElementById('historySuggestions').innerHTML=''; return; }
      const res = await fetch(API + '/customers?q=' + encodeURIComponent(q));
      const data = await res.json();
      const sug = document.getElementById('historySuggestions');
      sug.innerHTML = '';
      (data.customers||[]).forEach(c => {
        const a = document.createElement('a');
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = c.name + ' <small class="text-muted">(' + c.email + ')</small>';
        a.addEventListener('click', () => {
          search.value = c.email;
          sug.innerHTML = '';
          document.getElementById('selectedCustomerInfo').innerHTML = '<strong>' + c.name + '</strong> - ' + c.email;
          window.location.href = '/customers/' + c.id + '/orders';
        });
        sug.appendChild(a);
      });
    }, 300);
  });
});

async function loadOrderHistory(email) {
  document.getElementById('historyLoading').style.display = '';
  document.getElementById('historyTable').style.display = 'none';
  document.getElementById('historyEmpty').style.display = 'none';
  try {
    const res = await fetch(API + '/customers/' + encodeURIComponent(email) + '/orders');
    const data = await res.json();
    document.getElementById('historyLoading').style.display = 'none';
    if (!res.ok) { showToast('Unable to retrieve order history.','error'); return; }
    const orders = data.orders || [];
    if (orders.length === 0) { document.getElementById('historyEmpty').style.display = ''; showToast('No previous orders found for this customer.','success'); return; }
    const tbody = document.querySelector('#historyTable tbody');
    tbody.innerHTML = '';
    orders.forEach(o => {
      const units = (o.items||[]).reduce((a,i)=>a+i.quantity,0);
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${o.id}</td><td>${o.created_at}</td><td>${units}</td><td>?${o.subtotal}</td><td>?${o.tax}</td><td>?${o.grand_total}</td><td><button class="btn btn-sm btn-outline-primary viewDetails" data-order='${JSON.stringify(o).replace(/'/g,"&#39;")}'>View Details</button></td>`;
      tbody.appendChild(tr);
    });
    document.getElementById('historyTable').style.display = '';
    document.querySelectorAll('.viewDetails').forEach(btn => {
      btn.addEventListener('click', () => showOrderDetails(JSON.parse(btn.dataset.order)));
    });
    showToast('Order history loaded successfully.','success');
  } catch(e) {
    document.getElementById('historyLoading').style.display = 'none';
    showToast('Unable to retrieve order history.','error');
  }
}

function showOrderDetails(o) {
  const body = document.getElementById('orderDetailsBody');
  let itemsHtml = '';
  (o.items||[]).forEach((it,i) => {
    itemsHtml += `<tr><td>${i+1}</td><td>${it.product_name}</td><td>${it.quantity}</td><td>?${it.unit_price}</td><td>${it.tax_percentage}</td><td>?${it.subtotal}</td><td>?${it.tax}</td><td>?${it.total}</td></tr>`;
  });
  body.innerHTML = `
    <h6>Customer Information</h6>
    <p>${o.customer.name} - ${o.customer.email}</p>
    <h6>Order Information</h6>
    <p>Order ID: ${o.id} | Date: ${o.created_at}</p>
    <h6>Product Details</h6>
    <table class="table table-sm table-bordered"><thead class="table-light"><tr><th>S.No</th><th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Tax (%)</th><th>Subtotal</th><th>Tax Amount</th><th>Total</th></tr></thead><tbody>${itemsHtml}</tbody></table>
    <h6>Order Summary</h6>
    <p>Subtotal: ?${o.subtotal} | Tax: ?${o.tax} | Grand Total: ?${o.grand_total}</p>
  `;
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
  modal.show();
}
