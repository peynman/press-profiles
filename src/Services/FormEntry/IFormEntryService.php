<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Http\Request;
use Symfony\Component\Routing\Route;

interface IFormEntryService {

    /**
     * add new form entry
     *  if the user/session already has submitted this from, update it
     *  on event of update call $onProvide function(request, $inputNames, $form, $entry = null if entry is going to be created) array and set new values for form entry
     *
     * @param Request|null $request
     * @param mixed $user
     * @param int $formId
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry($request, $user, $formId, $tags = null, $onProvide = null);


    /**
     * add new form entry
     *  check on form_id and user_id only and update entries tag
     *
     * @param Request|null $request
     * @param mixed $user
     * @param int $formId
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateUserFormEntryTag($request, $user, $formId, $tags, $onProvide = null);

    /**
     * Undocumented function
     *
     * @param array $values
     * @return array
     */
    public function replaceBase64ImagesInInputs($values);
}
