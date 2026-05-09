<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use SoftDeletes;

    public const TYPE_VIDEO = 'video';
    public const TYPE_ARTICLE = 'article';
    public const TYPE_QUIZ = 'quiz';
    public const TYPE_LIVE = 'live';
    public const TYPE_ASSIGNMENT = 'assignment';

    protected $fillable = [
        'section_id',
        'title',
        'type',
        'content',
        'video_url',
        'video_path',
        'video_provider',
        'attachment',
        'attachment_name',
        'quiz_data',
        'duration',
        'is_preview',
        'order',
    ];
    protected $casts = [

        'quiz_data' => 'array',

        'is_preview' => 'boolean',
    ];
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
    public function isVideoLesson(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function isArticleLesson(): bool
    {
        return $this->type === self::TYPE_ARTICLE;
    }

    public function isQuizLesson(): bool
    {
        return $this->type === self::TYPE_QUIZ;
    }

    public function isLiveLesson(): bool
    {
        return $this->type === self::TYPE_LIVE;
    }

    public function isAssignmentLesson(): bool
    {
        return $this->type === self::TYPE_ASSIGNMENT;
    }

    public function isPreview(): bool
    {
        return $this->is_preview;
    }
    public function getVideoSourceAttribute(): ?string
    {
        return $this->video_url
            ?: $this->video_path;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) {
            return null;
        }

        return asset('storage/' . $this->attachment);
    }
    public function scopePublished($query)
    {
        return $query->whereHas(
            'section.course',
            fn ($q) => $q->where('is_published', true)
        );
    }

    public function scopePreview($query)
    {
        return $query->where(
            'is_preview',
            true
        );
    }
}