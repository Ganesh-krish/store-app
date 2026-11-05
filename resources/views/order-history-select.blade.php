<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Customer Order History</h3>
  <a href="/billing" class="btn btn-secondary btn-sm mb-3">Back</a>
  <div class="mb-3 position-relative">
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="text" id="historySearch" class="form-control" placeholder="Search customer by name or email">
    </div>
    <div id="historySuggestions" class="list-group position-absolute w-100" style="z-index:1000"></div>
  </div>
  <div id="selectedCustomerInfo" class="mb-2"></div>
  <div id="historyLoading" class="text-center" style="display:none;"><div class="spinner-border"></div></div>
  <div id="historyEmpty" class="text-muted" style="display:none;">No previous orders found.</div>
  <table class="table table-bordered" id="historyTable" style="display:none;">
    <thead class="table-light"><tr><th>S.No</th><th>Order Date</th><th>Items</th><th>Subtotal</th><th>Tax</th><th>Grand Total</th><th>Action</th></tr></thead>
    <tbody></tbody>
  </table>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="orderDetailsBody"></div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API='/api';
let historyTimer;
document.getElementById('historySearch').addEventListener('input', () => {
  clearTimeout(historyTimer);
  historyTimer = setTimeout(async () => {
    const q = document.getElementById('historySearch').value.trim();
    if (q.length < 1) { document.getElementById('historySuggestions').innerHTML=''; return; }
    const res = await fetch(API + '/customers?q=' + encodeURIComponent(q));
    const data = await res.json();
    const sug = document.getElementById('historySuggestions');
    sug.innerHTML = '';
    (data.customers||[]).forEach(c => {
      const a = document.createElement('a');
      a.className = 'list-group-item list-group-item-action';
      a.textContent = c.name + ' (' + c.email + ')';
      a.addEventListener('click', () => {
        document.getElementById('historySearch').value = c.email;
        sug.innerHTML = '';
        document.getElementById('selectedCustomerInfo').innerHTML = '<strong>Customer Information</strong><br>Name: ' + c.name;
        loadOrderHistory(c.email, c.id);
      });
      sug.appendChild(a);
    });
  }, 300);
});
async function loadOrderHistory(email, id) {
  document.getElementById('historyLoading').style.display = '';
  document.getElementById('historyTable').style.display = 'none';
  document.getElementById('historyEmpty').style.display = 'none';
  const res = await fetch(API + '/customers/' + encodeURIComponent(email) + '/orders');
  const data = await res.json();
  document.getElementById('historyLoading').style.display = 'none';
  const orders = data.orders || [];
  if (orders.length === 0) { document.getElementById('historyEmpty').style.display = ''; return; }
  const tbody = document.querySelector('#historyTable tbody');
  tbody.innerHTML = '';
  orders.forEach((o,i) => {
    const units = (o.items||[]).reduce((a,it)=>a+it.quantity,0);
    const tr = document.createElement('tr');
    tr.innerHTML = '<td>' + (i+1) + '</td><td>' + (o.created_at.split(' ')[0]) + '</td><td>' + units + '</td><td>&#8377;' + o.subtotal + '</td><td>&#8377;' + o.tax + '</td><td>&#8377;' + o.grand_total + '</td><td><button class="btn btn-sm btn-outline-primary viewDetails" data-order=\'' + JSON.stringify(o).replace(/'/g,"&#39;") + '\'>View Details</button></td>';
    tbody.appendChild(tr);
  });
  document.getElementById('historyTable').style.display = '';
  document.querySelectorAll('.viewDetails').forEach(btn => {
    btn.addEventListener('click', () => showOrderDetails(JSON.parse(btn.dataset.order)));
  });
}
function showOrderDetails(o) {
  const body = document.getElementById('orderDetailsBody');
  let itemsHtml = '';
  (o.items||[]).forEach((it,i) => {
    const sub = (parseFloat(it.unit_price)*it.quantity);
    const tax = sub*(parseFloat(it.tax_percentage)/100);
    itemsHtml += '<tr><td>' + (i+1) + '</td><td>' + it.product_name + '</td><td>' + it.quantity + '</td><td>&#8377;' + it.unit_price + '</td><td>' + it.tax_percentage + '</td><td>&#8377;' + sub.toFixed(2) + '</td><td>&#8377;' + tax.toFixed(2) + '</td><td>&#8377;' + (sub+tax).toFixed(2) + '</td></tr>';
  });
  body.innerHTML = '<h6>Customer Information</h6><p>Name: ' + o.customer.name + '</p><h6>Product Details</h6><table class="table table-sm table-bordered"><thead class="table-light"><tr><th>S.No</th><th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Tax (%)</th><th>Subtotal</th><th>Tax Amount</th><th>Total</th></tr></thead><tbody>' + itemsHtml + '</tbody></table>';
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
  modal.show();
}
</script>
</body>
</html>
