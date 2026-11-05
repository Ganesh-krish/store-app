<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Retail Counter Billing System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/css/billing.css" rel="stylesheet">
</head>
<body>
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>
<nav class="navbar navbar-dark bg-dark mb-3">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Retail Counter Billing System - Create Order</span>
<a class="btn btn-outline-light btn-sm ms-3" href="/order-history"><i class="bi bi-clock-history"></i> Order History</a>
  </div>
</nav>
<div class="container-fluid">
  <div class="card mb-3 p-3">
    <h5><i class="bi bi-people"></i> Customer Details</h5>
    <div class="row g-2 align-items-end">
      <div class="col-md-4 position-relative">
        <label class="form-label"><i class="bi bi-envelope"></i> Customer Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="email" id="custEmail" class="form-control" placeholder="Search customer" autocomplete="off">
        </div>
        <div id="emailSuggestions" class="list-group position-absolute w-100" style="z-index:1000"></div>
      </div>
      <div class="col-md-4">
        <label class="form-label"><i class="bi bi-person"></i> Customer Name</label>
        <input type="text" id="custName" class="form-control" placeholder="Customer name (autofilled)">
      </div>
      <div class="col-md-2">
        <button id="addCustomerBtn" class="btn btn-primary">Add Customer</button>
      </div>
    </div>
    <div id="custAlert" class="mt-2"></div>
  </div>

  <div class="card mb-3 p-3 billing-section" id="productsSection" style="display:none;">
    <h5><i class="bi bi-box-seam"></i> Products Details</h5>
    <table id="productTable" class="table table-bordered" style="background:#f1f3f5;">
      <thead class="table-light"><tr><th>S.No</th><th>Item Name</th><th>Qty</th><th>Unit Price</th><th>Tax (%)</th><th>Amount</th><th>Action</th></tr></thead>
      <tbody></tbody>
      <tfoot class="table-light"><tr>
        <td colspan="3" class="text-center fw-bold">Grand Total</td>
        <td></td>
        <td></td>
        <td id="totalAmount">₹0.00</td>
        <td></td>
      </tr></tfoot>
    </table>
    <div class="text-end"><span class="float-end"><button id="addProductBtn" class="btn btn-secondary btn-sm" style="width:auto;">Add Product</button></span></div>
  </div>

  <div class="row billing-section" id="bottomSection" style="display:none;">
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Low Stock Alerts</h5>
        <div class="mb-2">
          <label>Threshold</label>
          <input type="number" id="threshold" value="5" class="form-control form-control-sm" style="width:80px;display:inline-block">
          <button id="refreshLowStock" class="btn btn-sm btn-outline-secondary">Refresh</button>
        </div>
        <div id="lowStockList"></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Retail Bill Preview</h5>
        <div id="billPreview"></div>
      </div>
    </div>
  </div>

  <div id="orderAlert" class="mt-3"></div>
  <div class="text-center mt-3 mb-5 billing-section" id="generateSection" style="display:none;">
    <button id="generateBtn" class="btn btn-success btn-lg">Submit</button>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/billing.js"></script>
</body>
</html>
<!-- Order History Modal -->
<div class="modal fade" id="orderHistoryModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer Order History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 position-relative">
          <label class="form-label">Search customer</label>
          <input type="text" id="historySearch" class="form-control" placeholder="Name or email">
          <div id="historySuggestions" class="list-group position-absolute w-100" style="z-index:1000"></div>
        </div>
        <div id="selectedCustomerInfo" class="mb-2"></div>
        <div id="historyLoading" class="text-center" style="display:none;"><div class="spinner-border"></div></div>
        <div id="historyEmpty" class="text-muted" style="display:none;">No previous orders found.</div>
        <table class="table table-bordered" id="historyTable" style="display:none;">
          <thead class="table-light"><tr><th>Order ID</th><th>Order Date</th><th>Items</th><th>Subtotal</th><th>Tax</th><th>Grand Total</th><th>Action</th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
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
