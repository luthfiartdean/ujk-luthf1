<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cuci Express, Cuci Biasa, dll
            $table->text('description')->nullable();
            $table->decimal('price_per_kg', 8, 2); // Harga per kg
            $table->integer('duration_days')->default(1); // Durasi pengerjaan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_services');
    }
};