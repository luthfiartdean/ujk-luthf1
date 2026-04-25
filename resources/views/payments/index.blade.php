@extends('layouts.app')

@section('content')
<div class="row">

  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Riwayat Pembayaran</h5>
      <a href="/orders" class="btn btn-primary btn-sm">+ Buat Order</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($payments as $payment)
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="mb-1">{{ $payment->order->service->name ?? '-' }}</h6>
              <div class="text-muted small">
                Order #{{ $payment->order_id }} —
                {{ $payment->order->weight_kg ?? '-' }} kg
              </div>
              <div class="mt-2">
                <strong>Rp {{ number_format($payment->amount) }}</strong>
              </div>
              <div class="small text-muted mt-1">
                Metode:
                <span class="badge bg-secondary">{{ strtoupper($payment->payment_method) }}</span>
              </div>
              <div class="small text-muted">
                Tanggal: {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y H:i') : '-' }}
              </div>
            </div>
            <div>
              @if($payment->payment_status === 'paid')
                <span class="badge bg-success">LUNAS</span>
              @else
                <span class="badge bg-warning text-dark">BELUM BAYAR</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="alert alert-secondary">Belum ada riwayat pembayaran.</div>
    @endforelse
  </div>

</div>
@endsection