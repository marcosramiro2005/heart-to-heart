<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'category',
        'duration_days', 'emoji', 'color'
    ];

    public function userChallenges()
    {
        return $this->hasMany(UserChallenge::class);
    }

    public function isActiveForUser(int $userId): bool
    {
        return $this->userChallenges()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
    }
}