<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\User\Models\User;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
