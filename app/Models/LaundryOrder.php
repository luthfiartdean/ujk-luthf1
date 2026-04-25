<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'weight_kg',
        'total_price',
        'status',
        'notes',
        'pickup_date',
        'delivery_date',
    ];

    protected $casts = [
        'pickup_date'   => 'datetime',
        'delivery_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }

    // ✅ TAMBAHKAN INI
    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }
}