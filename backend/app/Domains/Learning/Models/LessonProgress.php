<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Lesson;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'completed_at',
    ];

    /**
     * Progress belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Progress belongs to lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}