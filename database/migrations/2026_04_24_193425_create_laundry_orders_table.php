<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('laundry_services')->onDelete('cascade');
            $table->decimal('weight_kg', 8, 2); // Berat cucian
            $table->decimal('total_price', 10, 2); // Total harga
            $table->enum('status', ['pending', 'processing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // Catatan khusus
            $table->timestamp('pickup_date')->nullable(); // Tanggal jemput
            $table->timestamp('delivery_date')->nullable(); // Tanggal pengiriman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};