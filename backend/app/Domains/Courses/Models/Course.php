<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Tag;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Analytics\Models\CourseView;
use App\Domains\Finance\Models\CourseSale;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'price',
        'level',
        'language',
        'duration',
        'requirements',
        'what_you_will_learn',
        'status',
        'is_published',
        'approved_at',
        'approved_by',
        'commission_percentage',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function views()
    {
        return $this->hasMany(CourseView::class);
    }
    public function sales()
    {
        return $this->hasOne(CourseSale::class);
    }
        public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
}