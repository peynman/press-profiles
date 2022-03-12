<?php


namespace Larapress\Profiles\Repository\Form;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Services\RepoSources\IRepositorySources;
use Larapress\Profiles\Models\Form;
use Larapress\Profiles\Models\FormEntry;

class FormRepository implements IFormRepository
{

    /** @var IRepositorySources */
    protected $sources;

    public function __construct(IRepositorySources $sources)
    {
        $this->sources = $sources;
    }

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @return Form[]
     */
    public function getFillableForms($user)
    {
        return Form::select('id', 'name', 'data')->get();
    }

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @param [type] $formId
     * @return Form
     */
    public function getForm($user, Request $request, $route, $formId)
    {
        $form = Form::find($formId);
        if (is_null($form)) {
            throw new AppException(AppException::ERR_OBJECT_NOT_FOUND);
        }

        if (isset($form->data['roles']) && count($form->data['roles']) > 0) {
            if (isset($form->data['roles'][0]['id'])) {
                $roles = collect($form->data['roles'])->pluck('id')->toArray();
            } else {
                $roles = isset($form->data['roles'][0]) ? $form->data['roles'] : array_keys($form->data['roles']);
            }
            if (count($roles) > 0) {
                if (is_null($user) || !$user->hasRole($roles)) {
                    throw new AppException(AppException::ERR_ACCESS_DENIED);
                }
            }
        }
        if (isset($form->data['blocks'])&& count($form->data['blocks']) > 0) {
            if (isset($form->data['blocks'][0]['id'])) {
                $roles = collect($form->data['blocks'])->pluck('id')->toArray();
            } else {
                $roles = isset($form->data['blocks'][0]) ? $form->data['blocks'] : array_keys($form->data['blocks']);
            }
            if (count($roles) > 0) {
                if (is_null($user) || $user->hasRole($roles)) {
                    throw new AppException(AppException::ERR_ACCESS_DENIED);
                }
            }
        }
        if (isset($form->data['registerred']) && !$form->data['registerred']) {
            throw new AppException(AppException::ERR_ACCESS_DENIED);
        }

        $form['sources'] = isset($form->data['sources']) && count($form->data['sources']) > 0 ?
            $this->sources->fetchRepositorySources($user, $form->data['sources'], $request, $route) : [];

        if (!is_null($user)) {
            $entry = FormEntry::query()
                ->where('user_id', $user->id)
                ->where('form_id', $form->id)
                ->where('domain_id', $user->getMembershipDomainId())
                ->first();

            if (!is_null($entry)) {
                $form['current-entry'] = $entry;
            }
        }

        return $form;
    }
}
