<?php

namespace Larapress\Profiles\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Larapress\Profiles\Services\FormEntry\Requests\FormFillRequest;

/**
 * @group Form Entries Management
 */
class FormEntryController extends Controller
{
    public static function registerPublicApiRoutes()
    {
        Route::post(config('larapress.profiles.routes.form_entries.name') . '/fill', '\\' . self::class . '@updateEntry')
            ->name(config('larapress.profiles.routes.form_entries.name') . '.any.fill-form-entry');
    }

    /**
     * Undocumented function
     *
     * @param IFormEntryService $serivce
     * @param FormFillRequest $request
     * @param string $formId
     * @return Illuminate\Http\Response
     */
    public function updateEntry(IFormEntryService $serivce, FormFillRequest $request)
    {
        return $serivce->updateFormEntry(
            Auth::user(),
            $request->getDomain()->id,
            $request->getForm(),
            $request->getSenderIP(),
            $request->getSenderAgent(),
            $request->getEntryData(),
            $request->getTags()
        );
    }
}
