<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SiteController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{game:title}', [GameController::class, 'show']);

Route::get('/games/{game:title}/sites', [SiteController::class, 'index']);
Route::get('/games/{game:title}/sites/{site:id}', [SiteController::class, 'show']);


Route::get('/games/{game:title}/articles', [ArticleController::class, 'index']);
Route::get('/games/{game:title}/articles/{article:id}', [ArticleController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::middleware('verified')->group(function () {
        Route::post('/games', [GameController::class, 'store']);
        Route::delete('/games/{game:title}', [GameController::class, 'destroy']);

        Route::post('/games/{game:title}/sites', [SiteController::class, 'store']);
        Route::patch('/games/{game:title}/sites/{site:id}', [SiteController::class, 'update']);
        Route::delete('/games/{game:title}/sites/{site:id}', [SiteController::class, 'destroy']);
        Route::get('/favorites/sites', [SiteController::class, 'favorites']);
        Route::get('/posts/sites', [SiteController::class, 'posts']);

        Route::post('/games/{game:title}/articles', [ArticleController::class, 'store']);
        Route::patch('/games/{game:title}/articles/{article:id}', [ArticleController::class, 'update']);
        Route::delete('/games/{game:title}/articles/{article:id}', [ArticleController::class, 'destroy']);
        Route::get('/favorites/articles', [ArticleController::class, 'favorites']);
        Route::get('/posts/articles', [ArticleController::class, 'posts']);

        Route::put('/games/{game:title}/favorite', [GameController::class, 'favorite']);
        Route::put('/sites/{site:id}/favorite', [SiteController::class, 'favorite']);
        Route::put('/articles/{article:id}/favorite', [ArticleController::class, 'favorite']);
        Route::delete('/games/{game:title}/favorite', [GameController::class, 'unfavorite']);
        Route::delete('/sites/{site:id}/favorite', [SiteController::class, 'unfavorite']);
        Route::delete('/articles/{article:id}/favorite', [ArticleController::class, 'unfavorite']);
    });
});
