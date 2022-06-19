<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Regist;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\RegisterController;

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

Route::group(['middleware' => 'api'], function($router){

    Route::prefix('auth')->group(function() {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });


    // Route::resource('blogs', BlogController::class);
    Route::post('blogs/{id}', [BlogController::class, 'update']);
    Route::apiResource('forums', 'ForumController');
    Route::apiResource('forums.comments', 'ForumCommentController');
    Route::resource('blogs', [BlogController::class]);
    //forums/{idforum}/comments/{idcomment}
});
