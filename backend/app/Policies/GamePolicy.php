<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->admin) {
            return true;
        }
    }

    public function destroy(User $user, Game $game): Response
    {
        return $game->user_id == $user->id
            ? $this->allow()
            : $this->deny('ユーザが所有するゲームでありません');
    }
}
