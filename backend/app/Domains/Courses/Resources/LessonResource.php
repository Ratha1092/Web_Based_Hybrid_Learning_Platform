<?php

namespace App\Domains\Courses\Resources;

use Illuminate\Database\Eloquent\Model;

class LessonResource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'resource_type',
        'file_path',
        'external_url'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}