@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Login</h3>
    <div id="login-alert"></div>
    <form id="login-form" onsubmit="event.preventDefault(); authLogin();">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input id="login-email" type="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input id="login-password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit">Login</button>
      <a class="btn btn-link" href="/register">Register</a>
    </form>
  </div>
</div>

@push('scripts')
<script>
async function authLogin() {
    const email    = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const alert    = document.getElementById('login-alert');

    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (res.ok && data.token) {
            // Simpan token JWT
            localStorage.setItem('token', data.token);

            // ✅ Login session web juga supaya bisa akses blade
            await fetch('/login-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email, password })
            });

            // Redirect ke orders
            window.location.href = '/orders';
        } else {
            alert.innerHTML = `<div class="alert alert-danger">${data.message ?? 'Login gagal.'}</div>`;
        }
    } catch (e) {
        alert.innerHTML = `<div class="alert alert-danger"@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Login</h3>
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    {{-- ✅ Ganti dari JS ke form POST biasa --}}
    <form method="POST" action="/login">
      @csrf
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit">Login</button>
      <a class="btn btn-link" href="/register">Register</a>
    </form>
  </div>
</div>
@endsection>Terjadi kesalahan, coba lagi.</div>`;
    }
}
</script>
@endpush
@endsection