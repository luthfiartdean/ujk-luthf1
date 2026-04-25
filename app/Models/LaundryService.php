<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price_per_kg', 'duration_days', 'is_active'];

    public function orders()
    {
        return $this->hasMany(LaundryOrder::class, 'service_id');
    }
}