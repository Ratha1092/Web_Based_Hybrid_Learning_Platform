<?php

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'learning_goals',
        'interests',
        'github',
        'linkedin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}