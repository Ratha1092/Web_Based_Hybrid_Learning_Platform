<?php

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\User\Models\User;

class InstructorApplication extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'experience',
        'status',
        'reviewed_at',
        'reviewed_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}