<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('laundry_orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['cash', 'transfer', 'qris'])->default('cash');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};