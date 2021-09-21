<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Http\Request;
use Larapress\Profiles\Models\Form;

interface IFormEntryService
{

    /**
     * add new form entry
     *  if the user/session already has submitted this from, update it
     *  on event of update call $onProvide function(request, $inputNames, $form, $entry = null if entry is going to be created) array and set new values for form entry
     *
     * @param Request|null $request
     * @param mixed $user
     * @param int|Form $form
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry($request, $user, $form, $tags = null, $onProvide = null);


    /**
     * add new form entry
     *  check on form_id and user_id only and update entries tag
     *
     * @param Request|null $request
     * @param mixed $user
     * @param int|Form $form
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateUserFormEntryTag($request, $user, $form, $tags, $onProvide = null);

    /**
     * Undocumented function
     *
     * @param int|Form $form
     *
     * @return array
     */
    public function getFormValidationRules($form);

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Form $form
     *
     * @return array
     */
    public function getValidatedFormInputs(Request $request, Form $form);
}
