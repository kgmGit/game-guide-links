<?php

namespace App\Models;

use App\Traits\CanFavorite;
use App\Traits\CanLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $game_id
 * @property int $user_id
 * @property string $title
 * @property string $outline
 * @property string $content
 */
class Article extends Model
{
    use HasFactory, CanFavorite, CanLike;

    protected $fillable = [
        'title',
        'outline',
        'content'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
