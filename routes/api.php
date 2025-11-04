<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;


Route::middleware('throttle:60,1')->apiResource('articles', ArticleController::class);
