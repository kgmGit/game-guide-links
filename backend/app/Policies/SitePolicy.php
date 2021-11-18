<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SitePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->admin) {
            return true;
        }
    }

    public function update(User $user, Site $site): Response
    {
        return $site->user_id == $user->id
            ? $this->allow()
            : $this->deny('ユーザが所有する攻略サイトではありません');
    }
}
