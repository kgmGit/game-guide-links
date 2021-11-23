<?php

namespace App\Http\Controllers;

use App\Http\Requests\Site\DestroyRequest;
use App\Http\Requests\Site\StoreRequest;
use App\Http\Requests\Site\UpdateRequest;
use App\Http\Resources\SiteResource;
use App\Models\Game;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    /**
     * サイト一覧取得
     *
     * @param Request $request
     * @param Game $game
     * @return AnonymousResourceCollection
     */
    public function index(Game $game): AnonymousResourceCollection
    {
        $game->load(['sites.user', 'sites.favorites', 'sites.likes', 'sites.game']);
        return SiteResource::collection($game->sites);
    }

    /**
     * サイト追加
     *
     * @param StoreRequest $request
     * @param Game $game
     * @return SiteResource
     */
    public function store(StoreRequest $request, Game $game): SiteResource
    {
        $validated = $request->validated();

        $site = auth()->user()->sites()->make($validated);
        $site->game()->associate($game);
        $site->save();

        return new SiteResource($site);
    }

    /**
     * サイト取得
     *
     * @param Game $game
     * @param Site $site
     * @return SiteResource
     */
    public function show(Game $game, Site $site): SiteResource
    {
        $site->load(['user', 'favorites', 'likes', 'game']);
        return new SiteResource($site);
    }

    /**
     * サイト更新
     *
     * @param UpdateRequest $request
     * @param Game $game
     * @param Site $site
     * @return SiteResource
     */
    public function update(UpdateRequest $request, Game $game, Site $site): SiteResource
    {
        $validated = $request->validated();

        $site->fill($validated)->save();

        return new SiteResource($site);
    }

    /**
     * サイト削除
     *
     * @param DestroyRequest $request
     * @param Game $game
     * @param Site $site
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Game $game, Site $site): JsonResponse
    {
        DB::beginTransaction();
        try {
            $site->favorites()->delete();
            $site->likes()->delete();
            $site->reports()->delete();
            $site->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, 'サイト削除処理中にエラーが発生しました');
        }

        return response()->json(null, 204);
    }

    /**
     * お気に入りサイト一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function favorites(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $sites = Site::query()->join('favorites', 'sites.id', '=', 'favorites.favorable_id')
            ->where('favorites.favorable_type', '=', 'App\\Models\\Site')
            ->where('favorites.user_id', '=', $user->id)
            ->select('sites.*')->with(['user', 'favorites', 'likes', 'game'])->get();

        return SiteResource::collection($sites);
    }

    /**
     * 投稿サイト一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function posts(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $sites = $user->sites()->with(['user', 'favorites', 'likes', 'game'])->get();

        return SiteResource::collection($sites);
    }

    /**
     * お気に入り登録
     *
     * @param Site $site
     * @return void
     */
    public function favorite(Site $site)
    {
        $site->registerFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * お気に入り解除
     *
     * @param Site $site
     * @return void
     */
    public function unfavorite(Site $site)
    {
        $site->unregisterFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * いいね登録
     *
     * @param Site $site
     * @return void
     */
    public function like(Site $site)
    {
        $site->registerLike(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * いいね解除
     *
     * @param Site $site
     * @return void
     */
    public function unlike(Site $site)
    {
        $site->unregisterLike(auth()->id());

        return response()->json(null, 204);
    }
}
