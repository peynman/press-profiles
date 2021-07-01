<?php

namespace Larapress\Profiles\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;

/**
 * Standard CRUD Controller for FormEntry resource.
 *
 * @group Form Entries Management
 */
class FormEntryController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.form_entries.name'),
            self::class,
            config('larapress.profiles.routes.form_entries.provider'),
        );
    }

    public static function registerPublicApiRoutes()
    {
        Route::post(config('larapress.profiles.routes.form_entries.name') . '/update/{form_id}', '\\' . self::class . '@updateEntry')
            ->name(config('larapress.profiles.routes.form_entries.name') . '.any.update-form-entry');
    }

    /**
     * Undocumented function
     *
     * @param IFormEntryService $serivce
     * @param Request $request
     * @param [type] $formId
     * @return Illuminate\Http\Response
     */
    public function updateEntry(IFormEntryService $serivce, Request $request, $formId)
    {
        return $serivce->updateFormEntry($request, Auth::user(), $formId);
    }
}
