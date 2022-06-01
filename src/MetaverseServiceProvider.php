<?php

namespace Jncinet\Metaverse;

use Illuminate\Support\ServiceProvider;

class MetaverseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'metaverse');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'metaverse');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/metaverse'),
            __DIR__.'/../config/metaverse.php' => config_path('metaverse.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/metaverse'),
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/metaverse'),
        ]);
    }
}
