@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Register</h3>
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form method="POST" action="/register">
      @csrf
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit">Register</button>
      <a class="btn btn-link" href="/login">Login</a>
    </form>
  </div>
</div>
@endsection