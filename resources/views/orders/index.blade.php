@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- HEADER --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Laundry Orders</h4>
      <small class="text-muted">Kelola pesanan laundry kamu</small>
    </div>
    <div class="d-flex gap-2">
      <a href="/payments" class="btn btn-outline-success btn-sm px-3">💳 Riwayat Bayar</a>
      <form method="POST" action="/logout">
        @csrf
        <button class="btn btn-outline-danger btn-sm px-3">Logout</button>
      </form>
    </div>
  </div>

  <div class="row g-4">

    {{-- FORM BUAT ORDER --}}
    <div class="col-md-4">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
          <h5 class="fw-semibold mb-3">Buat Order</h5>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <form method="POST" action="/orders">
            @csrf

            <div class="mb-3">
              <label class="form-label">Service</label>
              <select name="service_id" class="form-select" required>
                <option value="">Pilih service</option>
                @foreach($services as $service)
                  <option value="{{ $service->id }}">
                    {{ $service->name }} - Rp {{ number_format($service->price_per_kg) }}/kg
                  </option>
                @endforeach
              </select>
              @error('service_id')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Berat (kg)</label>
              <input type="number" name="weight_kg" step="0.1" min="0.5" class="form-control" required>
              @error('weight_kg')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Pickup Date</label>
              <input type="datetime-local" name="pickup_date" class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <button class="btn btn-primary w-100">+ Buat Order</button>
          </form>
        </div>
      </div>
    </div>

    {{-- LIST ORDERS --}}
    <div class="col-md-8">
      <h5 class="fw-semibold mb-3">My Orders</h5>

      @forelse($orders as $order)

      @php
        $statusColor = match($order->status) {
          'pending'    => 'secondary',
          'processing' => 'warning',
          'completed'  => 'success',
          'cancelled'  => 'danger',
          default      => 'dark'
        };
        $sudahBayar = $order->payments()->where('payment_status', 'paid')->exists();
      @endphp

      <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between">

            {{-- INFO ORDER --}}
            <div>
              <h6 class="fw-bold mb-1">{{ $order->service->name ?? '-' }}</h6>

              <div class="text-muted small mb-1">
                {{ $order->weight_kg }} kg • Rp {{ number_format($order->total_price) }}
              </div>

              <span class="badge bg-{{ $statusColor }} mb-2">
                {{ ucfirst($order->status) }}
              </span>

              @if($sudahBayar)
                <span class="badge bg-success mb-2">✓ Lunas</span>
              @else
                <span class="badge bg-danger mb-2">Belum Bayar</span>
              @endif

              <div class="small text-muted">
                📅 Pickup: {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y H:i') : '-' }}
              </div>
              <div class="small text-muted">
                🚚 Delivery: {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y H:i') : '-' }}
              </div>

              @if($order->notes)
                <div class="small mt-2">📝 {{ $order->notes }}</div>
              @endif
            </div>

            {{-- ACTION --}}
            <div style="width:200px">

              {{-- UPDATE STATUS --}}
              <form method="POST" action="/orders/{{ $order->id }}" class="mb-2">
                @csrf
                @method('PUT')
                <select name="status" class="form-select form-select-sm mb-1">
                  <option value="pending"    {{ $order->status=='pending'    ? 'selected' : '' }}>Pending</option>
                  <option value="processing" {{ $order->status=='processing' ? 'selected' : '' }}>Processing</option>
                  <option value="completed"  {{ $order->status=='completed'  ? 'selected' : '' }}>Completed</option>
                  <option value="cancelled"  {{ $order->status=='cancelled'  ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="text" name="notes"
                       value="{{ $order->notes }}"
                       class="form-control form-control-sm mb-1"
                       placeholder="Edit notes">
                <button class="btn btn-primary btn-sm w-100 mb-2">Update</button>
              </form>

              {{-- BAYAR --}}
              @if($sudahBayar)
                <button class="btn btn-success btn-sm w-100 mb-2" disabled>✓ Lunas</button>
              @else
                <a href="/payments/create/{{ $order->id }}"
                   class="btn btn-warning btn-sm w-100 mb-2">
                  💳 Bayar
                </a>
              @endif

              {{-- HAPUS --}}
              @if($order->status === 'pending')
                <form method="POST" action="/orders/{{ $order->id }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm w-100"
                          onclick="return confirm('Yakin hapus order ini?')">
                    Hapus
                  </button>
                </form>
              @endif

            </div>

          </div>
        </div>
      </div>

      @empty
        <div class="alert alert-light text-center border">
          Belum ada order
        </div>
      @endforelse

    </div>

  </div>
</div>
@endsection