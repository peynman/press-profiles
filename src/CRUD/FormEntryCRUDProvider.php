<?php

namespace Larapress\Profiles\CRUD;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Services\FormEntry\FormEntryUpdateEvent;
use Larapress\Profiles\Services\FormEntry\FormEntryUpdateReport;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Larapress\ECommerce\IECommerceUser;
use Larapress\FileShare\Services\FileUpload\IFileUploadService;
use Larapress\Profiles\Models\Form;

class FormEntryCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;

    public $name_in_config = 'larapress.profiles.routes.form_entries.name';
    public $model_in_config = 'larapress.profiles.routes.form_entries.model';
    public $compositions_in_config = 'larapress.profiles.routes.form_entries.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::DELETE,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
    ];
    public $createValidations = [
        'user_id' => 'required|exists:users,id',
        'form_id' => 'required|exists:forms,id',
        'tags' => 'nullable',
        'formValues.*' => 'nullable',
    ];
    public $updateValidations = [
        'user_id' => 'required|exists:users,id',
        'form_id' => 'required|exists:forms,id',
        'tags' => 'nullable',
        'formValues.*' => 'nullable',
    ];
    public $validSortColumns = [
        'id',
        'user_id',
        'domain_id',
        'form_id',
    ];
    public $defaultShowRelations = [
        'user',
        'form',
        'domain',
        'entry_tag_resolve',
    ];
    public $searchColumns = [
        'equals:tags',
        'has_exact:user,name',
        'has_exact:user.phones,number',
        'has:data->values',
    ];
    public $filterFields = [
        'created_from' => 'after:created_at',
        'created_to' => 'before:created_at',
        'type' => 'equals:type',
        'domain' => 'in:domain_id',
        'user_id' => 'equals:user_id',
        'form_id' => 'equals:form_id',
        'tags' => 'like:tags',
        'firstname' => 'like:data->values->firstname',
        'lastname' => 'like:data->values->lastname',
    ];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'user' => config('larapress.crud.user.provider'),
            'domain' => config('larapress.profiles.routes.domains.provider'),
            'form' => config('larapress.profiles.routes.forms.provider'),
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getReportSources(): array
    {
        return [
            new FormEntryUpdateReport(),
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate(array $args): array
    {
        /** @var IFileUploadService */
        $service = app(IFileUploadService::class);

        /** @var Form */
        $form = Form::find($args['form_id']);
        $values = $args['formValues'];
        if (isset($form->data['uploads']) && is_array($form->data['uploads'])) {
            foreach ($form->data['uploads'] as $uploadMeta) {
                $values = $service->replaceBase64WithFilePathValuesRecursuve(
                    $values,
                    $uploadMeta['prop'],
                    $uploadMeta['disk'],
                    $uploadMeta['folder']
                );
            }
        }
        $values = $service->replaceBase64WithFilePathValuesRecursuve(
            $values,
            null
        );

        $args['data'] = [
            'filledBy' => Auth::user()->id,
            'values' => $values,
        ];

        $class = config('larapress.crud.user.model');
        /** @var IProfileUser */
        $tUser = call_user_func([$class, 'find'], $args['user_id']);
        $args['domain_id'] = $tUser->getMembershipDomainId();

        return $args;
    }

    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeUpdate(array $args): array
    {
        return $this->onBeforeCreate($args);
    }

    /**
     * @param FormEntry $object
     *
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function onBeforeQuery($query): Builder
    {
        /** @var IECommerceUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
                $query->whereIn('domain_id', $user->getAffiliateDomainIds());
            }
        }

        return $query;
    }

    /**
     * Undocumented function
     *
     * @param FormEntry $object
     * @param array $input_data
     *
     * @return void
     */
    public function onAfterCreate($object, array $input_data): void
    {
        Helpers::forgetCachedValues(['user.forms:' . $object->user_id]);

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            true,
            'admin:' . Auth::user()->id,
            Carbon::now()
        );
    }

    /**
     * Undocumented function
     *
     * @param FormEntry $object
     * @param array $input_data
     * @return void
     */
    public function onAfterUpdate($object, $input_data): void
    {
        Helpers::forgetCachedValues(['user.forms:' . $object->user_id]);

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            false,
            'admin:' . Auth::user()->id,
            Carbon::now()
        );
    }

    /**
     * Undocumented function
     *
     * @param FormEntry $object
     *
     * @return void
     */
    public function onAfterDestroy($object): void
    {
        Helpers::forgetCachedValues([
            'user.forms:' . $object->user_id,
            'user.profile:' . $object->user_id,
            'user.support:' . $object->user_id,
            'user.introducer:' . $object->user_id
        ]);

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            false,
            'admin:' . Auth::user()->id,
            Carbon::now(),
        );
    }
}
