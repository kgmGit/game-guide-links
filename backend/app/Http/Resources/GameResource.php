<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'favorites_count' => $this->favorites()->count(),
            'favorited' => auth()->check()
                ? $this->favorites()->where('user_id', auth()->id())->exists()
                : false,
            'owner' => auth()->check() ? $this->user_id == auth()->id() : false,
        ];
    }
}
