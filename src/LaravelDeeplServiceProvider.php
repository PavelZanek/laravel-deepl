<?php

namespace PavelZanek\LaravelDeepl;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use PavelZanek\LaravelDeepl\Console\Commands\TranslateLangFilesCommand;
use PavelZanek\LaravelDeepl\Console\Commands\TranslateLangFolderCommand;
use PavelZanek\LaravelDeepl\Console\Commands\UsageCommand;

class LaravelDeeplServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-deepl.php', 'laravel-deepl');

        $this->app->singleton(DeeplClient::class, function ($app) {
            /** @var string $authKey */
            $authKey = config('laravel-deepl.api_key');

            return new DeeplClient($authKey, [
                'timeout' => config('laravel-deepl.timeout'),
                'max_retries' => config('laravel-deepl.retry_on_failures'),
            ]);
        });

        $this->app->alias(DeeplClient::class, 'deepl.translator');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        AboutCommand::add('Laravel Deepl', fn () => ['Version' => '1.0.0']);

        $this->publishes([
            __DIR__.'/../config/laravel-deepl.php' => config_path('laravel-deepl.php'),
        ], 'laravel-deepl-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-deepl-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                TranslateLangFilesCommand::class,
                TranslateLangFolderCommand::class,
                UsageCommand::class,
            ]);
        }
    }
}
