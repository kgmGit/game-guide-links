<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Article;
use App\Models\Game;
use App\Models\Report;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 通報一覧取得
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $reports = Report::leftJoin('sites', function ($join) {
            $join->on('reports.reportable_id', '=', 'sites.id')
                ->where('reports.reportable_type', '=', Site::class);
        })->leftJoin('articles', function ($join) {
            $join->on('reports.reportable_id', '=', 'articles.id')
                ->where('reports.reportable_type', '=', Article::class);
        })->leftJoin('games', function ($join) {
            $join->on('sites.game_id', '=', 'games.id')
                ->orOn('articles.game_id', '=', 'games.id')
                ->orOn(function ($query) {
                    $query->whereColumn('reports.reportable_id', 'games.id')
                        ->where('reports.reportable_type', '=', Game::class);
                });
        })->select('reports.*', 'games.title as game_title')
            ->with(['user'])->get();

        return ReportResource::collection($reports);
    }
}
