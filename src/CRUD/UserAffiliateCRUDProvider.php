<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\Core\Extend\Helpers;

class UserAffiliateCRUDProvider extends UserCRUDProvider
{
    public $data_keys = [
        'share_percent',
    ];

    public function getCreateRules(Request $request)
    {
        $rules = parent::getCreateRules($request);

        unset($rules['roles']);
        unset($rules['segments']);
        unset($rules['sub_domain_id']);

        return array_merge($rules, [
            'affiliate_domains' => 'required|objectIds:sub_domains,id,id',
            'share_percent' => 'nullable|numeric|max:100',
        ]);
    }

    public function getUpdateRules(Request $request)
    {
        $rules = parent::getUpdateRules($request);

        unset($rules['roles']);
        unset($rules['segments']);
        unset($rules['sub_domain_id']);

        return array_merge($rules, [
            'affiliate_domains' => 'required|objectIds:sub_domains,id,id',
            'share_percent' => 'required|numeric|max:100',
        ]);
    }

    public function getAutoSyncRelations()
    {
        return array_merge(parent::getAutoSyncRelations(), [
            'affiliate_domains',
        ]);
    }

    public function getValidRelations()
    {
        return ['affiliate_domains', 'roles'];
    }

    public function getEagerRelations()
    {
        return ['affiliate_domains'];
    }

    public function onBeforeCreate($args)
    {
        $args = parent::onBeforeCreate($args);
        if (isset($args['affiliate_domains'])) {
            $args['affiliate_domains'] = Helpers::getNormalizedObjectIds($args['affiliate_domains']);
        }

        if (! isset($args['share_percent']) || is_null($args['share_percent'])) {
            $args['share_percent'] = 0;
        }

        $args['roles'] = [config('bet.affiliate.role_id')];

        unset($args['segments']);
        unset($args['sub_domain_id']);

        $data = [];
        foreach ($this->data_keys as $key) {
            if (isset($args[$key])) {
                $data[$key] = $args[$key];
            }
        }
        $args['options'] = $data;

        return $args;
    }

    public function onBeforeUpdate($args)
    {
        $args = parent::onBeforeUpdate($args);
        if (isset($args['affiliate_domains'])) {
            $args['affiliate_domains'] = Helpers::getNormalizedObjectIds($args['affiliate_domains']);
        }

        unset($args['roles']);
        unset($args['segments']);
        unset($args['sub_domain_id']);

        $data = [];
        foreach ($this->data_keys as $key) {
            if (isset($args[$key])) {
                $data[$key] = $args[$key];
            }
        }
        $args['options'] = $data;

        return $args;
    }

    /**
     * @param \Larapress\Profiles\IProfileUser|\Larapress\CRUD\ICRUDUser $object
     * @param array            $args
     *
     * @return array|void
     */
    public function onAfterUpdate($object, $args)
    {
        $object->forgetDomainsCache();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function onBeforeQuery($query)
    {
        $query->whereHas('roles', function (Builder $q) {
            $q->whereIn('name', config('larapress.profiles.security.roles.affiliate'));
        });

        /** @var \Larapress\Profiles\IProfileUser|\Larapress\CRUD\ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->whereIn('domain_id', $user->getAffiliateDomainIds());
        }

        return $query;
    }
}
