<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price', 'stock', 'description', 'status'];

    protected $dates = ['created_at', 'updated_at']; // Ensure these are cast as dates

    // Optional: if you want to format the dates for serialization (API responses)
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
