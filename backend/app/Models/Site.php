<?php

namespace App\Models;

use App\Traits\CanFavorite;
use App\Traits\CanLike;
use App\Traits\CanReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_id
 * @property int $user_id
 * @property string $title
 * @property string $url
 * @property string $description
 */
class Site extends Model
{
    use HasFactory, CanFavorite, CanLike, CanReport;

    protected $fillable = [
        'title',
        'url',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
