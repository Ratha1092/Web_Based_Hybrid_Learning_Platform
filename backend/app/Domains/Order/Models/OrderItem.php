<?php

namespace App\Domains\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'course_id',
        'instructor_id',
        'course_title',
        'price',
        'commission_percentage',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function course()
    {
        return $this->belongsTo(\App\Domains\Course\Models\Course::class);
    }
}