<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanLike
{
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function likeUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'likable', 'likes')->withTimestamps();
    }

    public function registerLike(int $user_id): void
    {
        if (!$this->LikeUsers()->wherePivot('user_id', $user_id)->exists()) {
            $this->likeUsers()->attach($user_id);
        }
    }

    public function unregisterLike(int $user_id): void
    {
        $this->likeUsers()->detach($user_id);
    }
}
