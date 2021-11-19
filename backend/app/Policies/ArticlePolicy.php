<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->admin) {
            return true;
        }
    }

    public function update(User $user, Article $article): Response
    {
        return $article->user_id == $user->id
            ? $this->allow()
            : $this->deny('ユーザが所有する攻略記事ではありません');
    }

    public function destroy(User $user, Article $article): Response
    {
        return $article->user_id == $user->id
            ? $this->allow()
            : $this->deny('ユーザが所有する攻略記事ではありません');
    }
}
