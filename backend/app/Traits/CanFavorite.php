<?php

namespace App\Traits;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanFavorite
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }

    public function favoriteUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favorable', 'favorites')->withTimestamps();
    }

    public function registerFavorite(int $user_id): void
    {
        if (!$this->favoriteUsers()->wherePivot('user_id', $user_id)->exists()) {
            $this->favoriteUsers()->attach($user_id);
        }
    }

    public function unregisterFavorite(int $user_id): void
    {
        $this->favoriteUsers()->detach($user_id);
    }
}
