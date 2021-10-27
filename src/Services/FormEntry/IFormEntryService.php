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
     * @param mixed $user
     * @param int $domainId
     * @param int|string|Form $form
     * @param string $ip
     * @param string $agent
     * @param array $data
     * @param string|null $tags
     * @param callable $onProvide
     * @return FormEntry
     */
    public function updateFormEntry(
        $user,
        $domainId,
        $form,
        $ip,
        $agent,
        $data = [],
        $tags = null,
        $onProvide = null
    );

    /**
     * Undocumented function
     *
     * @param Form $form
     *
     * @return array
     */
    public function getFormValidationRules(Form $form): array;

    /**
     * Undocumented function
     *
     * @param Form $form
     * @param array $inputs
     *
     * @return array
     */
    public function getValidatedFormInputs(Form $form, array $inputs): array;
}
