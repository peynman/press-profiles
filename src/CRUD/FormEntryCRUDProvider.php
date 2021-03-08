<?php

namespace Larapress\Profiles\CRUD;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Services\FormEntry\FormEntryUpdateEvent;
use Larapress\Profiles\Services\FormEntry\FormEntryUpdateReport;
use Larapress\Reports\Services\IReportsService;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Larapress\ECommerce\IECommerceUser;

class FormEntryCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.form-entries.name';
    public $verbs = [
        self::VIEW,
        self::DELETE,
        self::REPORTS,
        self::CREATE,
        self::EDIT,
    ];
    public $model = FormEntry::class;
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
    public function getValidRelations()
    {
        return [
            'user' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name').'.view');
            },
            'domain' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.domains.name').'.view');
            },
            'user.phones' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.phone-numbers.name').'.view');
            },
            'user.form_support_user_profile' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_profile_default' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_profile_support' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.form_support_registration_entry' => function($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name').'.view');
            },
            'user.wallet_balance' => function($user) {
                return $user->hasPermission(config('larapress.ecommerce.routes.wallet_transactions.name').'.view');
            },
            'form',
            'entry_tag_resolve',
        ];
    }

    /**
     *
     */
    public function getReportSources()
    {
        /** @var IReportsService */
        $service = app(IReportsService::class);
        return [
            new FormEntryUpdateReport($service),
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $args
     * @return array
     */
    public function onBeforeCreate($args)
    {
        $request = Request::createFromGlobals();

        /** @var IFormEntryService */
        $service = app(IFormEntryService::class);
        $args['data'] = [
            'admin' => Auth::user()->id,
            'ip' => $request->ip(),
            'agent' => $request->userAgent(),
            'values' => $service->replaceBase64ImagesInInputs($args['formValues']),
        ];

        $class = config('larapress.crud.user.class');
        /** @var IProfileUser */
        $tUser = call_user_func([$class, 'find'], $args['user_id']);
        $args['domain_id'] = $tUser->getMembershipDomainId();

        return $args;
    }

    public function onBeforeUpdate($args)
    {
        $request = Request::createFromGlobals();

        /** @var IFormEntryService */
        $service = app(IFormEntryService::class);
        $args['data'] = [
            'admin' => Auth::user()->id,
            'ip' => $request->ip(),
            'agent' => $request->userAgent(),
            'values' => $service->replaceBase64ImagesInInputs($args['formValues']),
        ];

        $class = config('larapress.crud.user.class');
        /** @var IProfileUser */
        $tUser = call_user_func([$class, 'find'], $args['user_id']);
        $args['domain_id'] = $tUser->getMembershipDomainId();

        return $args;
    }

    /**
     * @param FormEntry $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return in_array($object->domain_id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var IECommerceUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if ($user->hasRole(config('larapress.ecommerce.lms.support_role_id'))) {
                $query->whereHas('user.form_entries', function ($q) use ($user) {
                    $q->where('tags', 'support-group-' . $user->id);
                });
            } else if ($user->hasRole(config('larapress.ecommerce.lms.owner_role_id'))) {
                $flatten_array = function ($arr) {
                    $flatten = [];
                    foreach ($arr as $item) {
                        if (gettype($item) === 'string') {
                            $flatten[] = $item;
                        } else {
                            foreach ($item as $inner) {
                                $flatten[] = $inner;
                            }
                        }
                    }
                    return $flatten;
                };

                $ownerEntries = $flatten_array(collect($user->getOwenedProductsIds())->map(function ($id) {
                    return ['course-'.$id.'-presence', 'course-'.$id.'-taklif', 'azmoon-'.$id];
                })->toArray());
                $query->whereIn('tags', $ownerEntries);
            } else {
                $query->whereIn('domain_id', $user->getAffiliateDomainIds());
            }
        }

        return $query;
}

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @param [type] $input_data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onAfterCreate($object, $input_data)
    {
        Cache::tags(['user.forms:' . $object->user_id])->flush();

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            true,
            'admin:' . Auth::user()->id,
            time()
        );
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @param [type] $input_data
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onAfterUpdate($object, $input_data)
    {
        Cache::tags(['user.forms:' . $object->user_id])->flush();

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            false,
            'admin:' . Auth::user()->id,
            time()
        );
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @return void
     */
    public function onAfterDestroy($object)
    {
        Cache::tags(['user.forms:' . $object->user_id])->flush();
        Cache::tags(['user.profile:' . $object->user_id])->flush();
        Cache::tags(['user.support:' . $object->user_id])->flush();
        Cache::tags(['user.introducer:' . $object->user_id])->flush();

        FormEntryUpdateEvent::dispatch(
            $object->user,
            $object->user->getMembershipDomain(),
            $object,
            $object->form,
            false,
            'admin:' . Auth::user()->id,
            time()
        );
    }
}
