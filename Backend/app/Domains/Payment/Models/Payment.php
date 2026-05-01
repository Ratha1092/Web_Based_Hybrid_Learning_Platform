<?php

namespace App\Domains\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_id',
        'external_reference',
        'idempotency_key',
        'amount',
        'currency',
        'status',
        'failure_reason',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(\App\Domains\Order\Models\Order::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}