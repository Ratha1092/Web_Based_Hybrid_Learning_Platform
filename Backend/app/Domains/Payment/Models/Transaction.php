<?php

namespace App\Domains\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Payment\Models\Payment;

class Transaction extends Model
{
    protected $fillable = [
        'payment_id',
        'gateway',
        'transaction_id',
        'status',
        'payload'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}