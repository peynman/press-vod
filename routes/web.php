<?php

use Illuminate\Support\Facades\Route;
use Larapress\VOD\Services\VOD\VODStreamController;

// api routes with public access
Route::middleware(config('larapress.crud.public-middlewares'))
    ->prefix('larapress.pages.prefix')
    ->group(function () {
        VODStreamController::registerPublicWebRoutes();
    });
