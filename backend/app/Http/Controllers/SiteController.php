<?php

namespace App\Http\Controllers;

use App\Http\Requests\Site\StoreRequest;
use App\Http\Requests\Site\UpdateRequest;
use App\Http\Resources\SiteResource;
use App\Models\Game;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        //
    }
}
