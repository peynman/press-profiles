<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\FormEntry;
use Larapress\Profiles\Services\FormEntryUpdateReport;
use Larapress\Reports\Services\IReportsService;

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
        'formValues.*' => 'required',
    ];
    public $updateValidations = [
        'user_id' => 'required|exists:users,id',
        'form_id' => 'required|exists:forms,id',
        'tags' => 'nullable',
        'formValues.*' => 'required',
    ];
    public $validSortColumns = [
        'id',
        'user_id',
        'domain_id',
        'form_id',
    ];
    public $validRelations = [
        'user',
        'form',
        'domain'
    ];
    public $defaultShowRelations = [
        'user',
        'form',
        'domain'
    ];
    public $searchColumns = [
        'id',
        'tags',
    ];
    public $filterFields = [
        'type' => 'equals:type',
        'domain' => 'in:domain_id',
        'user_id' => 'equals:user_id',
        'tags' => 'like:tags',
    ];

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

        $args['data'] = [
            'admin' => Auth::user()->id,
            'ip' => $request->ip(),
            'agent' => $request->userAgent(),
            'values' => $args['formValues'],
        ];

        return $args;
    }

    public function onBeforeUpdate($args)
    {
        $request = Request::createFromGlobals();

        $args['data'] = [
            'admin' => Auth::user()->id,
            'ip' => $request->ip(),
            'agent' => $request->userAgent(),
            'values' => $args['formValues'],
        ];

        return $args;
    }

    /**
     * @param FormEntry $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
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
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if (! $user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->orWhereIn('domain_id', $user->getAffiliateDomainIds());
            $query->orWhereHas('user.form_entries', function($q) use($user) {
                $q->where('tags', 'support-group-'.$user->id);
            });
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
        Cache::tags(['user.forms:'.$object->user_id])->flush();
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
        Cache::tags(['user.forms:'.$object->user_id])->flush();
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @return void
     */
    public function onAfterDestroy($object)
    {
        Cache::tags(['user.forms:'.$object->user_id])->flush();
        Cache::tags(['user.profile:'.$object->user_id])->flush();
        Cache::tags(['user.support:'.$object->user_id])->flush();
        Cache::tags(['user.introducer:'.$object->user_id])->flush();
    }
}
