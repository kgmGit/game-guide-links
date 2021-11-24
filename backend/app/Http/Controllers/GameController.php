<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\DestroyRequest;
use App\Http\Requests\Game\StoreRequest;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\ReportResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{

    /**
     * ゲーム一覧取得
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $title = $request->query('title');

        if (!$title) {
            return GameResource::collection([]);
        }

        $games = Game::with(['favorites'])->where('title', 'LIKE', "%$title%")->limit(5)->get();

        return GameResource::collection($games);
    }

    /**
     * ゲーム追加
     *
     * @param StoreRequest $request
     * @return GameResource
     */
    public function store(StoreRequest $request): GameResource
    {
        $validated = $request->validated();

        $user = auth()->user();
        $game = $user->games()->create($validated);

        return new GameResource($game);
    }

    /**
     * ゲーム取得
     *
     * @param Game $game
     * @return GameResource
     */
    public function show(Game $game): GameResource
    {
        return new GameResource($game);
    }


    /**
     * ゲーム削除
     *
     * @param DestroyRequest $request
     * @param Game $game
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Game $game): JsonResponse
    {
        if ($game->sites()->exists() || $game->articles()->exists()) {
            abort(422, '攻略サイトまたは攻略記事が紐付いているゲームは削除できません');
        }

        DB::beginTransaction();
        try {
            $game->favorites()->delete();
            $game->reports()->delete();
            $game->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, 'ゲーム削除処理中にエラーが発生しました');
        }

        return response()->json(null, 204);
    }

    /**
     * お気に入りゲーム一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function favorites(): AnonymousResourceCollection
    {
        $user = auth()->user();
        $games = $user->favoriteGames()->with(['favorites'])->get();

        return GameResource::collection($games);
    }

    /**
     * お気に入り登録
     *
     * @param Game $game
     * @return JsonResponse
     */
    public function favorite(Game $game): JsonResponse
    {
        $game->registerFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * お気に入り解除
     *
     * @param Game $game
     * @return JsonResponse
     */
    public function unfavorite(Game $game): JsonResponse
    {
        $game->unregisterFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * 通報登録
     *
     * @param ReportRequest $request
     * @param Game $game
     * @return ReportResource
     */
    public function report(ReportRequest $request, Game $game): ReportResource
    {
        $report = $game->createReport(auth()->user(), $request->validated()['content']);
        $report->game_title = $game->title;

        return new ReportResource($report);
    }
}
