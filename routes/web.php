<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\MoviesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [MoviesController::class, 'index'])->name('movies.index');
Route::post('/', [MoviesController::class, 'index'])->name('movies.search');
Route::get('/page/{page}', [MoviesController::class, 'index'])->name('movies.paginate');

Auth::routes(['verify'=>true]);

Route::get('/film/{slug}', [MoviesController::class, 'show'])->name('movies.show');

Route::group(['middleware' => ['auth','verified']], function () {

    Route::post('/kinoklad/{movie}/rate', [MoviesController::class, 'rate'])->name('movies.rate');
    Route::post('/kinoklad/{movie}/comments', [CommentController::class, 'store'])->name('comments.store');
});