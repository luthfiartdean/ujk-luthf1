<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  {{-- ✅ TAMBAHKAN INI — wajib untuk CSRF di fetch/axios --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Laundry App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="/">Laundry</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto" id="nav-links">
        <li class="nav-item"><a class="nav-link" href="/login" id="nav-login">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="/register" id="nav-register">Register</a></li>
        <li class="nav-item d-none" id="nav-profile-item"><a class="nav-link" href="/orders" id="nav-profile">Dashboard</a></li>
        <li class="nav-item d-none" id="nav-logout-item"><a class="nav-link" href="#" id="nav-logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="/js/app.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    updateNav();
    document.getElementById('nav-logout')?.addEventListener('click', (e) => {
      e.preventDefault();
      logout();
    });
  });
</script>
{{-- ✅ TAMBAHKAN INI — supaya @push('scripts') di setiap view bisa jalan --}}
@stack('scripts')
</body>
</html>