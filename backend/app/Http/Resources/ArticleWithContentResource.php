<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleWithContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'outline' => $this->outline,
            'content' => $this->content,
            'favorites_count' => $this->favorites->count(),
            'favorited' => auth()->check()
                ? !$this->favorites->where('user_id', auth()->id())->isEmpty()
                : false,
            'likes_count' => $this->likes->count(),
            'liked' => auth()->check()
                ? !$this->likes->where('user_id', auth()->id())->isEmpty()
                : false,
            'owner' => auth()->check() ? $this->user_id == auth()->id() : false,
            'owner_name' => $this->user ? $this->user->name : null,
            'game_title' => $this->game->title,
        ];
    }
}