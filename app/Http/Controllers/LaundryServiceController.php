<?php

namespace App\Http\Controllers;

use App\Models\LaundryService;
use Illuminate\Http\Request;

class LaundryServiceController extends Controller
{
    // GET all services
    public function index()
    {
        try {
            $services = LaundryService::where('is_active', true)->get();
            return response()->json([
                'message' => 'Services berhasil diambil',
                'data' => $services,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil services',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // GET single service
    public function show($id)
    {
        try {
            $service = LaundryService::find($id);
            if (!$service) {
                return response()->json([
                    'message' => 'Service tidak ditemukan',
                ], 404);
            }
            return response()->json([
                'message' => 'Service berhasil diambil',
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil service',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // CREATE service
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price_per_kg' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);

        try {
            $service = LaundryService::create([
                'name' => $request->name,
                'description' => $request->description,
                'price_per_kg' => $request->price_per_kg,
                'duration_days' => $request->duration_days,
                'is_active' => true,
            ]);

            return response()->json([
                'message' => 'Service berhasil dibuat',
                'data' => $service,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat service',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // UPDATE service
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'price_per_kg' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $service = LaundryService::find($id);
            if (!$service) {
                return response()->json([
                    'message' => 'Service tidak ditemukan',
                ], 404);
            }

            $service->update($request->all());

            return response()->json([
                'message' => 'Service berhasil diupdate',
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal update service',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // DELETE service
    public function destroy($id)
    {
        try {
            $service = LaundryService::find($id);
            if (!$service) {
                return response()->json([
                    'message' => 'Service tidak ditemukan',
                ], 404);
            }

            $service->delete();

            return response()->json([
                'message' => 'Service berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal hapus service',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}