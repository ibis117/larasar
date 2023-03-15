<?php

namespace Ibis117\Larasar;

use Ibis117\Larasar\Commands\CrudGenerator;
use Illuminate\Support\ServiceProvider;

class LarasarServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ibis117');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ibis117');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/larasar.php', 'larasar');

        // Register the service the package provides.
        $this->app->singleton('larasar', function ($app) {
            return new Larasar;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['larasar'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/larasar.php' => config_path('larasar.php'),
        ], 'larasar.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ibis117'),
        ], 'larasar.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ibis117'),
        ], 'larasar.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ibis117'),
        ], 'larasar.views');*/

        // Registering package commands.
         $this->commands([
             CrudGenerator::class
         ]);
    }
}
