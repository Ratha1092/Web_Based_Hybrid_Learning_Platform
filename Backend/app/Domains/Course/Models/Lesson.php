<?php

namespace App\Domains\Course\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'section_id',
        'title',
        'content',
        'video_url',
        'duration',
        'order'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}