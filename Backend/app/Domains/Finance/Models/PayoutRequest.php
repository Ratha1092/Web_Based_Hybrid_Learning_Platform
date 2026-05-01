<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\User\Models\User;

class PayoutRequest extends Model
{
    protected $fillable = [
        'instructor_id',
        'amount',
        'payment_method',
        'status',
        'requested_at',
        'processed_at'
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}