<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Tampilkan semua payment milik user
    public function index()
    {
        $payments = Payment::with('order.service')
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('payments.index', compact('payments'));
    }

    // Form bayar order
    public function create($orderId)
    {
        $order = LaundryOrder::where('id', $orderId)
                             ->where('user_id', Auth::id())
                             ->firstOrFail();

        // Cek sudah dibayar belum
        $existing = Payment::where('order_id', $orderId)
                           ->where('payment_status', 'paid')
                           ->first();

        if ($existing) {
            return redirect('/payments')->with('error', 'Order ini sudah dibayar.');
        }

        return view('payments.create', compact('order'));
    }

    // Simpan payment
    public function store(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:laundry_orders,id',
            'payment_method' => 'required|in:cash,transfer,ewallet',
        ]);

        $order = LaundryOrder::where('id', $request->order_id)
                             ->where('user_id', Auth::id())
                             ->firstOrFail();

        Payment::create([
            'order_id'       => $order->id,
            'amount'         => $order->total_price,
            'payment_method' => $request->payment_method,
            'payment_status' => 'paid',
            'payment_date'   => now(),
        ]);

        // Update status order jadi processing
        $order->update(['status' => 'processing']);

        return redirect('/payments')->with('success', 'Pembayaran berhasil!');
    }
}