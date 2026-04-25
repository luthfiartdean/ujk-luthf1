// app.js - simple frontend helper for JWT API
axios.defaults.baseURL = window.location.origin + '/api';

// token helpers
const TOKEN_KEY = 'jwt_token';
function setToken(token) {
  localStorage.setItem(TOKEN_KEY, token);
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
  updateNav();
}
function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}
function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
  delete axios.defaults.headers.common['Authorization'];
  updateNav();
}

// init axios header if token exists
if (getToken()) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + getToken();
}

// update nav links visibility
function updateNav() {
  const hasToken = !!getToken();
  document.getElementById('nav-login')?.classList.toggle('d-none', hasToken);
  document.getElementById('nav-register')?.classList.toggle('d-none', hasToken);
  document.getElementById('nav-profile-item')?.classList.toggle('d-none', !hasToken);
  document.getElementById('nav-logout-item')?.classList.toggle('d-none', !hasToken);
}

// auth actions
async function authRegister() {
  const name = document.getElementById('register-name').value;
  const email = document.getElementById('register-email').value;
  const phone = document.getElementById('register-phone').value;
  const password = document.getElementById('register-password').value;

  try {
    const res = await axios.post('/register', { name, email, phone, password });
    setToken(res.data.token);
    showAlert('register-alert', 'success', 'Register berhasil. Anda otomatis login.');
    window.location.href = '/orders';
  } catch (err) {
    const msg = err.response?.data?.message || (err.response?.data?.error) || err.message;
    showAlert('register-alert', 'danger', msg);
  }
}

async function authLogin() {
  const email = document.getElementById('login-email').value;
  const password = document.getElementById('login-password').value;

  try {
    const res = await axios.post('/login', { email, password });
    setToken(res.data.token);
    showAlert('login-alert', 'success', 'Login berhasil.');
    window.location.href = '/orders';
  } catch (err) {
    const msg = err.response?.data?.message || err.message;
    showAlert('login-alert', 'danger', msg);
  }
}

async function logout() {
  try {
    if (getToken()) {
      await axios.post('/logout');
    }
  } catch (e) {
    // ignore
  } finally {
    clearToken();
    window.location.href = '/login';
  }
}

// helpers
function showAlert(containerId, type, message) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
}

// SERVICES
async function fetchServices() {
  try {
    const res = await axios.get('/services');
    return res.data.data || res.data;
  } catch (err) {
    console.error(err);
    return [];
  }
}

async function populateServiceSelect() {
  const services = await fetchServices();
  const sel = document.getElementById('service-select');
  if (!sel) return;
  sel.innerHTML = '<option value="">-- pilih service --</option>';
  services.forEach(s => {
    const opt = document.createElement('option');
    opt.value = s.id;
    opt.textContent = `${s.name} — Rp ${Number(s.price_per_kg).toLocaleString()} /kg (${s.duration_days} hari)`;
    sel.appendChild(opt);
  });
}

// ORDERS
async function fetchOrdersAndRender() {
  try {
    const res = await axios.get('/orders');
    renderOrders(res.data.data || res.data);
  } catch (err) {
    if (err.response?.status === 401) {
      clearToken();
      window.location.href = '/login';
    } else {
      showAlert('order-alert', 'danger', 'Gagal mengambil orders');
    }
  }
}

function renderOrders(orders) {
  const container = document.getElementById('orders-list');
  if (!container) return;
  if (!orders || orders.length === 0) {
    container.innerHTML = '<div class="alert alert-secondary">Belum ada order</div>';
    return;
  }
  const rows = orders.map(o => {
    const pickup = o.pickup_date ? moment(o.pickup_date).format('YYYY-MM-DD HH:mm') : '-';
    const delivery = o.delivery_date ? moment(o.delivery_date).format('YYYY-MM-DD HH:mm') : '-';
    return `
      <div class="card mb-2">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6>${o.service?.name || 'Service tidak ada'}</h6>
              <div>Berat: ${o.weight_kg} kg — Total: Rp ${Number(o.total_price).toLocaleString()}</div>
              <div>Status: <strong>${o.status}</strong></div>
              <div>Pickup: ${pickup} — Delivery: ${delivery}</div>
              <div>Notes: ${o.notes || '-'}</div>
            </div>
            <div class="text-end">
              <button class="btn btn-sm btn-outline-primary mb-1" onclick="updateOrderStatus(${o.id}, 'processing')">Processing</button>
              <button class="btn btn-sm btn-outline-success mb-1" onclick="updateOrderStatus(${o.id}, 'completed')">Complete</button>
              <button class="btn btn-sm btn-outline-danger mb-1" onclick="cancelOrder(${o.id})">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');
  container.innerHTML = rows;
}

async function createOrder() {
  const service_id = document.getElementById('service-select').value;
  const weight_kg = document.getElementById('order-weight').value;
  const pickup_date = document.getElementById('order-pickup').value;
  const notes = document.getElementById('order-notes').value;

  try {
    const res = await axios.post('/orders', { service_id, weight_kg, pickup_date: pickup_date || null, notes });
    showAlert('order-alert', 'success', 'Order berhasil dibuat');
    // refresh orders
    await fetchOrdersAndRender();
    // clear form
    document.getElementById('order-form').reset();
  } catch (err) {
    const msg = err.response?.data?.message || err.message;
    showAlert('order-alert', 'danger', msg);
  }
}

async function updateOrderStatus(orderId, status) {
  try {
    await axios.put(`/orders/${orderId}`, { status });
    await fetchOrdersAndRender();
  } catch (err) {
    showAlert('order-alert', 'danger', 'Gagal update order');
  }
}

async function cancelOrder(orderId) {
  if (!confirm('Batalkan order ini?')) return;
  try {
    await axios.delete(`/orders/${orderId}`);
    await fetchOrdersAndRender();
  } catch (err) {
    showAlert('order-alert', 'danger', 'Gagal batalkan order');
  }
}

// page boots
document.addEventListener('DOMContentLoaded', () => {
  // if on orders page, populate services and orders
  if (document.getElementById('service-select')) {
    if (!getToken()) {
      window.location.href = '/login';
      return;
    }
    populateServiceSelect();
    fetchOrdersAndRender();
  }

  // if services list page
  if (document.getElementById('services-list')) {
    fetchServices().then(renderServicesList);
  }
});

function renderServicesList(services) {
  const container = document.getElementById('services-list');
  if (!container) return;
  if (!services || services.length === 0) {
    container.innerHTML = '<div class="alert alert-secondary">Tidak ada layanan</div>';
    return;
  }
  container.innerHTML = services.map(s => `
    <div class="card mb-2">
      <div class="card-body">
        <h5>${s.name}</h5>
        <div>${s.description || ''}</div>
        <div>Rp ${Number(s.price_per_kg).toLocaleString()} /kg — ${s.duration_days} hari</div>
      </div>
    </div>
  `).join('');
}