<?php

namespace Larapress\VOD\Providers;

use Illuminate\Support\ServiceProvider;
use Larapress\VOD\Commands\VODCreateProductType;
use Larapress\VOD\Services\VOD\IVODStreamService;
use Larapress\VOD\Services\VOD\VODStreamService;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IVODStreamService::class, VODStreamService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'larapress');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        $this->publishes([
            __DIR__.'/../../config/vod.php' => config_path('larapress/vod.php'),
        ], ['config', 'larapress', 'larapress-vod']);

        if ($this->app->runningInConsole()) {
            $this->commands([
                VODCreateProductType::class,
            ]);
        }
    }
}
