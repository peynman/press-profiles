<?php

namespace Larapress\Profiles\Services;

use Illuminate\Http\Request;
use Symfony\Component\Routing\Route;

interface IFormEntryService {
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param int $formId
     * @return FormEntry
     */
    public function updateFormEntry(Request $request, $formId);
}
