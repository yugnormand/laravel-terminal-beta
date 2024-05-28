<?php

use Illuminate\Support\Facades\Route;
use Todocoding\LaravelTerminalBeta\Http\Controllers\AssetController;

/*
|--------------------------------------------------------------------------
| Assets Routes
|--------------------------------------------------------------------------
| These routes are used to serve the assets for the Laravel Shell.
|
*/

Route::group(config('laravel-terminal.route'), function () {
    Route::get('assets/{asset}', [AssetController::class, 'serve'])->name('laravel-terminal.assets.serve');
});