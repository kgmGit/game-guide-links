<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleWithContentResource;
use App\Models\Article;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //
    }
}
