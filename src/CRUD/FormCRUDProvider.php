<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Form;

class FormCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.forms.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = Form::class;
    public $createValidations = [
        'name' => 'required|string',
        'data' => 'required|json',
        'flags' => 'nullable|int',
    ];
    public $updateValidations = [
        'name' => 'required|string',
        'data' => 'required|json',
        'flags' => 'nullable|int',
    ];
    public $autoSyncRelations = [
        'author' => 'auth::user',
    ];
    public $validSortColumns = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
    public $validRelations = [
        'author',
        'entries',
    ];
    public $validFilters = [

    ];
    public $defaultShowRelations = [

    ];
    public $excludeFromUpdate = [
    ];
    public $searchColumns = [
        'name',
        'data',
    ];
    public $filterDefaults = [
    ];
    public $filterFields = [
    ];

    /**
     * @param Form $object
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $domain_ids = $object->domains->pluck('id');
            $aff_domains = $user->getAffiliateDomainIds();

            foreach ($domain_ids as $domainId) {
                if (in_array($domainId, $aff_domains)) {
                    return true;
                }
            }

            return false;
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

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereHas('domains', function(Builder $q) use($user) {
                $q->whereIn('id', $user->getAffiliateDomainIds());
            });
        }

        return $query;
    }

}
