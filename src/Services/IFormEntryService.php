<?php

namespace Larapress\Profiles\Services;

use Illuminate\Http\Request;
use Symfony\Component\Routing\Route;

interface IFormEntryService {

    /**
     * add new form entry
     *  if the user/session already has submitted this from, update it
     *  on event of update call $onUpdate function(request, $inputNames, $form, $entry) array and set new values for form entry
     *
     * @param Request $request
     * @param int $formId
     * @param string|null $tags
     * @param callable $onUpdate
     * @return FormEntry
     */
    public function updateFormEntry(Request $request, $formId, $tags = null, $onUpdate = null);
}
