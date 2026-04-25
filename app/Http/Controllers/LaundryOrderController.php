<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\LaundryService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaundryOrderController extends Controller
{
    // 🔹 Tampilkan semua order (Blade)
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $orders = LaundryOrder::where('user_id', $user->id)
            ->with('service')
            ->latest()
            ->get();

        $services = LaundryService::all();

        return view('orders.index', compact('orders', 'services'));
    }

    // 🔹 API (optional)
    public function indexApi()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = LaundryOrder::where('user_id', $user->id)
            ->with('service')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // 🔹 Simpan order
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:laundry_services,id',
            'weight_kg'  => 'required|numeric|min:0.5',
            'notes'      => 'nullable|string',
            'pickup_date'=> 'nullable|date',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $service = LaundryService::findOrFail($request->service_id);

        $total_price = $request->weight_kg * $service->price_per_kg;

        $pickup_date = $request->pickup_date
            ? Carbon::parse($request->pickup_date)
            : now();

        $delivery_date = (clone $pickup_date)->addDays($service->duration_days);

        $order = LaundryOrder::create([
            'user_id'       => $user->id,
            'service_id'    => $request->service_id,
            'weight_kg'     => $request->weight_kg,
            'total_price'   => $total_price,
            'status'        => 'pending',
            'notes'         => $request->notes,
            'pickup_date'   => $pickup_date,
            'delivery_date' => $delivery_date,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Order berhasil dibuat',
                'data'    => $order
            ], 201);
        }

        return redirect()->back()->with('success', 'Order berhasil dibuat');
    }

    // 🔹 Update order
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $order = LaundryOrder::where('id', $id)
            ->where('user_id', $user->id) // 🔥 proteksi user
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes'  => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Order berhasil diupdate']);
        }

        return redirect()->back()->with('success', 'Order berhasil diupdate');
    }

    // 🔹 Hapus / cancel order
    public function destroy($id)
    {
        $user = auth()->user();

        $order = LaundryOrder::where('id', $id)
            ->where('user_id', $user->id) // 🔥 proteksi user
            ->firstOrFail();

        if ($order->status !== 'pending') {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Hanya order pending yang bisa dihapus'
                ], 422);
            }

            return redirect()->back()->with('error', 'Hanya order pending yang bisa dihapus');
        }

        $order->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Order berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Order berhasil dihapus');
    }
}