<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'location',
        'birth_date',
        'profile_public',
        'show_in_forum',
        'theme_color',
        'onboarding_completado',
        'objetivo_principal',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'birth_date'            => 'date',
            'profile_public'        => 'boolean',
            'show_in_forum'         => 'boolean',
            'onboarding_completado' => 'boolean',
        ];
    }

    // ── Relaciones ──

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

    public function savedResources()
    {
        return $this->belongsToMany(Resource::class, 'resource_saves')
            ->withTimestamps();
    }

    // ── Helpers ──

    public function totalPoints(): int
    {
        return $this->achievements()->sum('points');
    }

    public function nivelActual(): string
    {
        $logros = $this->achievements()->count();

        if ($logros >= 12) return 'Maestro del Bienestar';
        if ($logros >= 9)  return 'Experto Emocional';
        if ($logros >= 6)  return 'Explorador';
        if ($logros >= 3)  return 'Aprendiz';
        if ($logros >= 1)  return 'Iniciado';
        return 'Semilla';
    }

    public function rachaActual(): int
    {
        $registros = $this->emotionalRecords()
            ->orderByDesc('created_at')
            ->pluck('created_at')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($registros->isEmpty()) return 0;

        if ($registros[0] !== now()->toDateString() &&
            $registros[0] !== now()->subDay()->toDateString()) {
            return 0;
        }

        $racha = 1;
        for ($i = 0; $i < $registros->count() - 1; $i++) {
            $actual   = Carbon::parse($registros[$i]);
            $anterior = Carbon::parse($registros[$i + 1]);
            if ($actual->diffInDays($anterior) === 1) {
                $racha++;
            } else {
                break;
            }
        }

        return $racha;
    }
}