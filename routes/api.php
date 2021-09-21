<?php

use Illuminate\Support\Facades\Route;
use Larapress\Profiles\Controllers\FormEntryController;
use Larapress\Profiles\Services\ProfileUser\ProfileUserController;

Route::middleware(config('larapress.crud.middlewares'))
    ->prefix(config('larapress.crud.prefix'))
    ->group(function () {
        ProfileUserController::registerApiRoutes();
    });


Route::middleware(config('larapress.crud.public-middlewares'))
    ->prefix(config('larapress.crud.prefix'))
    ->group(function () {
        FormEntryController::registerPublicApiRoutes();
    });
