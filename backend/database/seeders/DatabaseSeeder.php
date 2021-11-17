<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Favorite;
use App\Models\Game;
use App\Models\Like;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ユーザ登録
        User::factory()->admin()->create([
            'email' => 'admin@admin.com',
        ]);
        User::factory()->create([
            'email' => 'test@test.com',
        ]);
        User::factory()->unverified()->create([
            'email' => 'test2@test.com',
        ]);
        User::factory()->count(3)->create();

        // ゲーム登録
        $timestampsArray = ['created_at' => now(), 'updated_at' => now()];
        $users_verified = User::whereNotNull('email_verified_at')->where('admin', false)->get();
        $games = collect();
        foreach ($users_verified as $user) {
            $games = $games->merge(
                Game::factory()->for($user)->count(5)->make()
            );
        }
        Game::insert(
            $games->map(function (Game $game) use ($timestampsArray) {
                return $game->attributesToArray() + $timestampsArray;
            })->toArray()
        );


        $games = Game::all();
        $favorites = collect();
        $sites = collect();
        $articles = collect();
        $reports = collect();
        foreach ($games as $game) {
            // ゲームお気に入り
            $favoriteUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newFavorites = $favoriteUsers->map(function (User $user) use ($game): Favorite {
                $favorite = $game->favorites()->make();
                return $favorite->user()->associate($user);
            });
            $favorites = $favorites->merge($newFavorites);

            // ゲーム通報
            $reportUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newReports = $reportUsers->map(function (User $user) use ($game): Report {
                return Report::factory()->for($user)->for($game, 'reportable')->make();
            });
            $reports = $reports->merge($newReports);

            // サイト
            $newSites = $users_verified->map(function (User $user) use ($game): Collection {
                return Site::factory()->for($user)->for($game)->count(3)->make();
            });
            $sites = $sites->merge($newSites->collapse());

            // 記事
            $newArticles = $users_verified->map(function (User $user) use ($game): Collection {
                return Article::factory()->for($user)->for($game)->count(3)->make();
            });
            $articles = $articles->merge($newArticles->collapse());
        }

        Site::insert(
            $sites->map(function (Site $site) use ($timestampsArray) {
                return $site->attributesToArray() + $timestampsArray;
            })->toArray()
        );
        Article::insert(
            $articles->map(function (Article $article) use ($timestampsArray) {
                return $article->attributesToArray() + $timestampsArray;
            })->toArray()
        );

        $sites = Site::all();
        $likes = collect();
        foreach ($sites as $site) {
            // サイトお気に入り
            $favoriteUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newFavorites = $favoriteUsers->map(function (User $user) use ($site): Favorite {
                $favorite = $site->favorites()->make();
                return $favorite->user()->associate($user);
            });
            $favorites = $favorites->merge($newFavorites);

            // サイトいいね
            $likeUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newLikes = $likeUsers->map(function (User $user) use ($site): Like {
                $like = $site->likes()->make();
                return $like->user()->associate($user);
            });
            $likes = $likes->merge($newLikes);

            // サイト通報
            $reportUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newReports = $reportUsers->map(function (User $user) use ($site): Report {
                return Report::factory()->for($user)->for($site, 'reportable')->make();
            });
            $reports = $reports->merge($newReports);
        }

        $articles = Article::all();
        foreach ($articles as $article) {
            // 記事お気に入り
            $favoriteUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newFavorites = $favoriteUsers->map(function (User $user) use ($article): Favorite {
                $favorite = $article->favorites()->make();
                return $favorite->user()->associate($user);
            });
            $favorites = $favorites->merge($newFavorites);

            // 記事いいね
            $likeUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newLikes = $likeUsers->map(function (User $user) use ($article): Like {
                $like = $article->likes()->make();
                return $like->user()->associate($user);
            });
            $likes = $likes->merge($newLikes);

            // 記事通報
            $reportUsers = $users_verified->random(rand(0, $users_verified->count() - 1));
            $newReports = $reportUsers->map(function (User $user) use ($article): Report {
                return Report::factory()->for($user)->for($article, 'reportable')->make();
            });
            $reports = $reports->merge($newReports);
        }

        $favoritesArray = $favorites->map(function (Favorite $favorite) use ($timestampsArray) {
            return $favorite->attributesToArray() + $timestampsArray;
        })->toArray();
        for ($i = 0; $i < count($favoritesArray); $i += 100) {
            Favorite::insert(array_slice($favoritesArray, $i, 100));
        }

        $likesArray = $likes->map(function (Like $like) use ($timestampsArray) {
            return $like->attributesToArray() + $timestampsArray;
        })->toArray();
        for ($i = 0; $i < count($likesArray); $i += 100) {
            Like::insert(array_slice($likesArray, $i, 100));
        }

        $reportsArray = $reports->map(function (Report $report) use ($timestampsArray) {
            return $report->attributesToArray() + $timestampsArray;
        })->toArray();
        for ($i = 0; $i < count($reportsArray); $i += 100) {
            Report::insert(array_slice($reportsArray, $i, 100));
        }
    }
}
