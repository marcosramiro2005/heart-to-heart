<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function emotionalRecords()
{
    return $this->hasMany(EmotionalRecord::class);
}

public function chatMessages()
{
    return $this->hasMany(ChatMessage::class);
}

public function communityPosts()
{
    return $this->hasMany(CommunityPost::class);
}

public function breathingSessions()
{
    return $this->hasMany(BreathingSession::class);
}

public function forumPosts()
{
    return $this->hasMany(ForumPost::class);
}

public function forumLikes()
{
    return $this->hasMany(ForumLike::class);
}

public function savedNews()
{
    return $this->hasMany(SavedNews::class);
}

public function achievements()
{
    return $this->belongsToMany(Achievement::class, 'user_achievements')
        ->withPivot('unlocked_at')
        ->withTimestamps();
}

public function totalPoints(): int
{
    return $this->achievements()->sum('points');
}
}
