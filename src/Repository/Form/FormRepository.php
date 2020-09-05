<?php


namespace Larapress\Profiles\Repository\Form;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Larapress\CRUD\Services\ICRUDService;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Mews\Captcha\Facades\Captcha;

class FormRepository implements IFormRepository
{
    /**
     * Undocumented function
     *
     * @param [type] $user
     * @return Form[]
     */
    public function getFillableForms($user) {
        return Form::select('id', 'name', 'data')->get();
    }

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @param [type] $formId
     * @return Form
     */
    public function getForm($user, Request $request, Route $route, $formId)
    {
        $form = Form::find($formId);
        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        if (isset($form->data['roles'])) {
            if (isset($form->data['roles'][0]['id'])) {
                $roles = collect($form->data['roles'])->pluck('id')->toArray();
            } else {
                $roles = array_keys($form->data['roles']);
            }
            if (count($roles) > 0) {
                if (is_null($user) || !$user->hasRole($roles)) {
                    throw new AppException(AppException::ERR_ACCESS_DENIED);
                }
            }
        }

        $form['sources'] = isset($form->data['sources']) && count($form->data['sources']) > 0 ?
            $this->getFormDataSources($user, $request, $route, $form->data['sources']) : [];

        if (!is_null($user)) {
            /** @var IDomainRepository */
            $domainRepo = app(IDomainRepository::class);
            $domain = $domainRepo->getRequestDomain($request);

            $entry = FormEntry::query()
                ->where('user_id', $user->id)
                ->where('form_id', $form->id)
                ->where('domain_id', $domain->id)
                ->first();

            if (!is_null($entry)) {
                $form['current-entry'] = $entry;
            }
        }

        return $form;
    }


    /**
     * Undocumented function
     *
     * @param [type] $user
     * @param Request $request
     * @param Route $route
     * @param array $sources
     * @return array
     */
    public function getFormDataSources($user, Request $request, Route $route, $inputSources)
    {
        $sources = [];
        /** @var ICRUDService */
        $crudService = app(ICRUDService::class);
        foreach ($inputSources as $source) {
            $res = [];
            switch ($source['resource']) {
                case 'object':
                    switch ($source['class']) {
                        case 'captcha':
                            $res = Captcha::create('default', true);
                            break;
                        default:
                            $provider = new $source['class'];
                            $crudService->useProvider($provider);
                            $res = $crudService->show($request, $route->parameter($source['param']));
                            break;
                    }
                    break;
                case 'repository':
                    $repo = $source['class'];
                    $safeRepos = config('larapress.pages.safe-sources');
                    if (in_array($repo, $safeRepos)) {
                        $args =  isset($source['args']) ? $source['args'] : [];
                        $repoRef = app()->make($repo);

                        $methodArgs = [];
                        usort($args, function ($a, $b) {
                            return $a['index'] > $b['index'];
                        });
                        foreach ($args as $arg) {
                            if (isset($arg['type'])) {
                                switch ($arg['type']) {
                                    case 'json':
                                        $methodArgs[] = json_decode($arg['value']);
                                        break;
                                    case 'request':
                                        $methodArgs[] = $request;
                                        break;
                                    case 'route':
                                        $methodArgs[] = $route;
                                        break;
                                    case 'param':
                                        $methodArgs[] = $route->parameter($arg['value']);
                                        break;
                                }
                            }
                        }
                        $res = call_user_func([$repoRef,  $source['method']], $user, ...$methodArgs);
                    }
                    break;
            }

            $sources[] = [
                'resource' => $res,
                'path' => $source['path']
            ];
        }
        return $sources;
    }
}
