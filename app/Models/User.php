<?php

namespace App\Models;

use App\Models\Forum;
use App\Models\ForumComment;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function blogs() {
        return $this->hasMany(Blog::class);
    }

    public function forums() {
        return $this->hasMany(Forum::class);
    }

    public function forumComments() {
        return $this->hasMany(ForumComment::class);
    }

    public function getJWTIdentifier()
    {
     return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
