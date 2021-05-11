<?php

use Illuminate\Support\Facades\Route;
use Larapress\VOD\Services\VOD\VODStreamController;

// api routes with public access
Route::middleware(config('larapress.pages.middleware'))
    ->prefix('larapress.pages.prefix')
    ->group(function () {
        VODStreamController::registerPublicWebRoutes();
    });
