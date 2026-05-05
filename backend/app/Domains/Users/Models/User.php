<?php

namespace App\Domains\Users\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Orders\Models\Order;
use App\Domains\Analytics\Models\CourseView;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Users\Models\InstructorProfile;
use App\Domains\Users\Models\StudentProfile;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Auth\Models\EmailVerificationToken;
use App\Domains\Auth\Models\PasswordResetToken;
use App\Domains\Auth\Models\TwoFactorCode;
use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\OAuthAccount;

class User extends Authenticatable
{   
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'role',
        'instructor_status',
        'status',
        'last_login_at',
        'oauth_provider',
        'oauth_id',
        'oauth_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Courses created by instructor
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Courses the student enrolled in
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Lesson progress tracking
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Reviews written by the user
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Wishlist courses
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Orders created by user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function courseViews()
    {
        return $this->hasMany(CourseView::class);
    }
    public function revenueShares()
    {
        return $this->hasMany(RevenueShare::class, 'instructor_id');
    }
    public function wallet()
    {
        return $this->hasOne(InstructorWallet::class, 'instructor_id');
    }
    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'instructor_id');
    }
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Instructor verification request
     */
    public function instructorVerification()
    {
        return $this->hasOne(InstructorVerification::class);
    }

    /**
     * Check if user is a verified instructor
     */
    public function isVerifiedInstructor(): bool
    {
        return $this->role === 'instructor' && $this->instructor_status === 'verified';
    }

    /**
     * Check if instructor verification is pending
     */
    public function hasVerificationPending(): bool
    {
        return $this->role === 'instructor' && $this->instructor_status === 'pending';
    }

    /**
     * Check if verification was rejected
     */
    public function hasVerificationRejected(): bool
    {
        return $this->role === 'instructor' && $this->instructor_status === 'rejected';
    }

    /**
     * Email verification tokens
     */
    public function emailVerificationTokens()
    {
        return $this->hasMany(EmailVerificationToken::class);
    }

    /**
     * Password reset tokens
     */
    public function passwordResetTokens()
    {
        return $this->hasMany(PasswordResetToken::class);
    }

    /**
     * Two factor codes
     */
    public function twoFactorCodes()
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    /**
     * Activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * OAuth accounts
     */
    public function oauthAccounts()
    {
        return $this->hasMany(OAuthAccount::class);
    }

    /**
     * Can create courses (verified instructor)
     */
    public function canCreateCourses(): bool
    {
        return $this->isVerifiedInstructor();
    }
}