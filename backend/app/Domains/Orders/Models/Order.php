<?php

namespace App\Domains\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Users\Models\User;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'discount_amount',
        'final_amount',
        'currency',
        'status',
        'payment_status',
        'customer_name',
        'customer_email',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // RELATIONS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(\App\Domains\Payments\Models\Payment::class);
    }
}