@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <h5>Bayar Order</h5>

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        <h6>Detail Order</h6>
        <table class="table table-sm mb-0">
          <tr>
            <td class="text-muted">Service</td>
            <td><strong>{{ $order->service->name ?? '-' }}</strong></td>
          </tr>
          <tr>
            <td class="text-muted">Berat</td>
            <td>{{ $order->weight_kg }} kg</td>
          </tr>
          <tr>
            <td class="text-muted">Pickup</td>
            <td>{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="text-muted">Delivery</td>
            <td>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y H:i') : '-' }}</td>
          </tr>
          <tr>
            <td class="text-muted">Total</td>
            <td><strong class="text-primary">Rp {{ number_format($order->total_price) }}</strong></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6>Pilih Metode Pembayaran</h6>
        <form method="POST" action="/payments">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}">

          <div class="mb-3">
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
              <label class="form-check-label" for="cash">💵 Cash</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer">
              <label class="form-check-label" for="transfer">🏦 Transfer Bank</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_method" id="ewallet" value="ewallet">
              <label class="form-check-label" for="ewallet">📱 E-Wallet</label>
            </div>
            @error('payment_method')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success w-100"
                    onclick="return confirm('Konfirmasi pembayaran Rp {{ number_format($order->total_price) }}?')">
              Bayar Sekarang
            </button>
            <a href="/payments" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection