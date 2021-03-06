<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\DestroyRequest;
use App\Http\Requests\Article\StoreRequest;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleWithContentResource;
use App\Http\Resources\ReportResource;
use App\Models\Article;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * 記事一覧取得
     *
     * @param Game $game
     * @return AnonymousResourceCollection
     */
    public function index(Game $game): AnonymousResourceCollection
    {
        $game->load(['articles.user', 'articles.favorites', 'articles.likes', 'articles.game']);
        return ArticleResource::collection($game->articles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 記事追加
     *
     * @param StoreRequest $request
     * @param Game $game
     * @return ArticleWithContentResource
     */
    public function store(StoreRequest $request, Game $game): ArticleWithContentResource
    {
        $validated = $request->validated();
        $article = auth()->user()->articles()->make($validated);
        $article->game()->associate($game);
        $article->save();

        return new ArticleWithContentResource($article);
    }

    /**
     * 記事取得
     *
     * @param Game $game
     * @param Article $article
     * @return ArticleResource
     */
    public function show(Game $game, Article $article): ArticleWithContentResource
    {
        $article->load(['user', 'favorites', 'likes', 'game']);
        return new ArticleWithContentResource($article);
    }

    /**
     * 記事更新
     *
     * @param UpdateRequest $request
     * @param Game $game
     * @param Article $article
     * @return ArticleWithContentResource
     */
    public function update(UpdateRequest $request, Game $game, Article $article): ArticleWithContentResource
    {
        $validated = $request->validated();

        $article->fill($validated)->save();

        return new ArticleWithContentResource($article);
    }

    public function destroy(DestroyRequest $request, Game $game, Article $article)
    {
        DB::beginTransaction();
        try {
            $article->favorites()->delete();
            $article->likes()->delete();
            $article->reports()->delete();
            $article->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, '記事削除処理中にエラーが発生しました');
        }

        return response()->json(null, 204);
    }

    /**
     * お気に入り記事一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function favorites(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $articles = $user->favoriteArticles()->with(['user', 'favorites', 'likes', 'game'])->get();

        return ArticleResource::collection($articles);
    }

    /**
     * 投稿記事一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function posts(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $articles = $user->articles()->with(['user', 'favorites', 'likes', 'game'])->get();

        return ArticleResource::collection($articles);
    }

    /**
     * お気に入り登録
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function favorite(Article $article): JsonResponse
    {
        $article->registerFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * お気に入り解除
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function unfavorite(Article $article): JsonResponse
    {
        $article->unregisterFavorite(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * いいね登録
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function like(Article $article): JsonResponse
    {
        $article->registerLike(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * いいね解除
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function unlike(Article $article): JsonResponse
    {
        $article->unregisterLike(auth()->id());

        return response()->json(null, 204);
    }

    /**
     * 通報登録
     *
     * @param ReportRequest $request
     * @param Article $article
     * @return ReportResource
     */
    public function report(ReportRequest $request, Article $article): ReportResource
    {
        $report = $article->createReport(auth()->user(), $request->validated()['content']);
        $report->game_title = $article->game->title;

        return new ReportResource($report);
    }
}
