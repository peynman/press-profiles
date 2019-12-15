<?php

use Illuminate\Support\Facades\Route;
use Larapress\CRUD\Middleware\CRUDAuthorize;
use Larapress\Profiles\CRUDControllers as CRUDControllers;
use Larapress\Profiles\Middleware\AppSettingOverride;

Route::middleware(['auth:api', AppSettingOverride::class, CRUDAuthorize::class])
    ->group(function () {
        CRUDControllers\ActivateCodeController::registerRoutes();
        CRUDControllers\ActivateCodeHistoryController::registerRoutes();
        CRUDControllers\EmailAddressController::registerRoutes();
        CRUDControllers\PhoneNumberController::registerRoutes();
        CRUDControllers\SettingsController::registerRoutes();
        CRUDControllers\DomainController::registerRoutes();
        CRUDControllers\UserController::registerRoutes();
    });
