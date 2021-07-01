<?php


namespace Larapress\Profiles\Repository\Form;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

interface IFormRepository
{

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @return Form[]
     */
    public function getFillableForms($user);

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
     * @param Route|null $route
     * @param array $sources
     * @return array
     */
    public function getFormDataSources($user, Request $request, $route, $sources);
}
