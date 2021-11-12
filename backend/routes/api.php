<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
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

Route::get('/games/{game:title}/articles', [ArticleController::class, 'index']);
Route::get('/games/{game:title}/articles/{article:id}', [ArticleController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::middleware('verified')->group(function () {
        Route::post('/games', [GameController::class, 'store']);
    });
});
