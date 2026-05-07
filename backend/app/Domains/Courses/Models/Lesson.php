<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'section_id',
        'title',
        'type',
        'content',
        'video_url',
        'video_path',
        'quiz_data',
        'duration',
        'is_preview',
        'order'
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'is_preview' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
