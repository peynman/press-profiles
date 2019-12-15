<?php


namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Flags\DomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\ICRUDUser;

class DomainCRUDProvider implements ICRUDProvider
{
    use BaseCRUDProvider;

    public $model = Domain::class;
    public $createValidations = [
        'name' => 'required|string|unique:domains,name',
        'title' => 'required|string',
        'domain' => 'required|string',
        'ips' => 'required|string|ip_list',
        'nameservers' => 'required|string',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|string|json',
    ];
    public $updateValidations = [
        'title' => 'required|string',
        'domain' => 'required|string',
        'ips' => 'required|string|',
        'nameservers' => 'required|string',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|string|json',
    ];
    public $autoSyncRelations = [];
    public $validSortColumns = [
        'id',
        'name',
        'title',
        'created_at',
        'domain',
        'ips',
        'nameservers'
    ];
    public $validRelations = ['author'];
    public $validFilters = [];
    public $defaultShowRelations = ['author'];
    public $excludeFromUpdate = ['name'];
    public $searchColumns = ['name', 'title'];
    public $filterDefaults = [];
    public $filterFields = [];

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->where('author_id', $user->id);
        }

        return $query;
    }

    /**
     * @param array $args
     * @return array
     */
    public function onBeforeCreate($args)
    {
        if (!isset($args['flags'])) {
            $args['flags'] = 0;
        }

        /** @var ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $args['author_id'] = $user->id;
        }

        return $args;
    }

    /**
     * @param array $args
     * @return array
     */
    public function onBeforeUpdate($args)
    {
        if (!isset($args['flags'])) {
            $args['flags'] = 0;
        }

        return $args;
    }

    /**
     * @param Domain $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            return in_array($object->id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param Domain $object
     * @param array  $input_data
     *
     * @return array|void
     */
    public function onAfterCreate($object, $input_data)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $user->domains()->attach($object->id, [
                'flags' => DomainFlags::AFFILIATE_DOMAIN
            ]);
            $user->forgetAffiliateDomainsCache();
        }
    }

    /**
     * @param Domain $object
     * @param array  $input_data
     *
     * @return array|void
     */
    public function onAfterUpdate($object, $input_data)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        $user->forgetAffiliateDomainsCache();
    }
}
