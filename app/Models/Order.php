<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Shipping;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  'total_amount', 'status', 'payment_status', 'payment_method',
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
