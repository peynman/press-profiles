<?php


namespace Larapress\Profiles\Repository\Form;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

interface IFormRepository {
    /**
     * Undocumented function
     *
     * @param [type] $user
     * @param [type] $formId
     * @return Form
     */
    public function getForm($user, Request $request, Route $route, $formId);

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @param Request $request
     * @param Route $route
     * @param array $sources
     * @return array
     */
    public function getFormDataSources($user, Request $request, Route $route, $sources);
}
