<?php

use Illuminate\Support\Facades\Route;
use Todocoding\LaravelTerminalBeta\Http\Livewire\Terminal;

/*
|--------------------------------------------------------------------------
| Laravel Shell Routes
|--------------------------------------------------------------------------
| These are the routes used by Laravel Shell.
|
*/

Route::group(config('laravel-terminal.route'), function () {
    Route::get('/', Terminal::class)->name('laravel-terminal.terminal');
});

require __DIR__.'/assets.php';