<?php

namespace Todocoding\LaravelTerminalBeta;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LaravelTerminalBetaServiceProvider extends ServiceProvider
{
    /**
     * The current version of Laravel Shell.
     *
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishConfig();

        if (config('laravel-terminal.enabled')) {
            $this->registerRoutes();
            $this->registerViews();
            $this->registerLivewireComponents();
        }
    }

    /**
     * Register the package config.
     */
    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/laravel-terminal.php', 'laravel-terminal');
    }

    /**
     * Publish the package config.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/config/laravel-terminal.php' => config_path('laravel-terminal.php'),
        ], 'config');
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    /**
     * Register the package views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-terminal');
    }

    /**
     * Register the livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('laravel-terminal::terminal', \Todocoding\LaravelTerminalBeta\Http\Livewire\Terminal::class);
    }
}