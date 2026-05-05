<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Course;

class Enrollment extends Model
{
    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}