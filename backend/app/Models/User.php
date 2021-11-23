<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property bool $admin
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * メールアドレス検証メール送信
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * パスワードリセットメール送信
     *
     * @param [type] $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function favoriteGames()
    {
        return $this->morphedByMany(Game::class, 'favorable', 'favorites')->withTimestamps();
    }

    public function favoriteSites()
    {
        return $this->morphedByMany(Site::class, 'favorable', 'favorites')->withTimestamps();
    }

    public function favoriteArticles()
    {
        return $this->morphedByMany(Article::class, 'favorable', 'favorites')->withTimestamps();
    }

    public function likeSites()
    {
        return $this->morphedByMany(Site::class, 'likable', 'likes')->withTimestamps();
    }

    public function likeArticles()
    {
        return $this->morphedByMany(Article::class, 'likable', 'likes')->withTimestamps();
    }

    public function reportGames()
    {
        return $this->morphedByMany(Game::class, 'reportable', 'reports')->withTimestamps();
    }

    public function reportSites()
    {
        return $this->morphedByMany(Site::class, 'reportable', 'reports')->withTimestamps();
    }

    public function reportArticles()
    {
        return $this->morphedByMany(Article::class, 'reportable', 'reports')->withTimestamps();
    }
}
