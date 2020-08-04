<?php

namespace Larapress\Profiles\Services;

use Illuminate\Http\Request;
use Symfony\Component\Routing\Route;

interface IFormEntryService {

    /**
     * add new form entry
     *  if the user/session already has submitted this from, update it
     *  on event of update call $onProvide function(request, $inputNames, $form, $entry = null if entry is going to be created) array and set new values for form entry
     *
     * @param Request $request
     * @param int $formId
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry(Request $request, $formId, $tags = null, $onProvide = null);
}
