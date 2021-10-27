<?php

namespace Larapress\Profiles\Services\FormEntry;

use Larapress\Profiles\Models\Form;

interface IFormContentProvider
{
    /**
     * Undocumented function
     *
     * @return array
     */
    public function getFormRules(Form $form): array;

    /**
     * Undocumented function
     *
     * @param array $inputs
     * @return array
     */
    public function getFormValidInputs(Form $form, array $inputs): array;
}
