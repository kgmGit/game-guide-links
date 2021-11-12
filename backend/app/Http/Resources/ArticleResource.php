<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    private bool $withContent = false;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param bool $withContent
     * @return void
     */
    public function __construct($resource, bool $withContent = false)
    {
        $this->withContent = $withContent;
        parent::__construct($resource);
    }

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
            'content' => $this->when($this->withContent, $this->content),
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
        ];
    }
}