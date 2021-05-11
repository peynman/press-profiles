<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\Services\CRUD\BaseCRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\DomainSub;

class DomainCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.domains.name';
    public $extend_in_config = 'larapress.profiles.routes.domains.extend.providers';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = Domain::class;
    public $createValidations = [
        'domain' => 'required|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'sub_domains.*.sub_domain' => 'required|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'ips' => 'required|string|ip_list',
        'nameservers' => 'nullable|string',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|json',
        // used by super-role account to create domains in place of other users
        'target_user_id' => 'nullable|exists:users,id',
    ];
    public $updateValidations = [
        'domain' => 'required|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'sub_domains.*.sub_domain' => 'required|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'ips' => 'required|string|ip_list',
        'nameservers' => 'required|string',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|json',
        // used by super-role account to create domains in place of other users
        'target_user_id' => 'nullable|exists:users,id',
    ];
    public $searchColumns = [
        'domain',
        'id',
        'ips',
        'nameservers',
    ];
    public $validSortColumns = [
        'id',
        'domain',
        'ips',
        'author_id',
        'nameservers',
        'created_at',
        'updated_at',
    ];
    public $validRelations = [
        'author',
        'sub_domains',
    ];
    public $defaultShowRelations = [
        'author',
        'sub_domains',
    ];

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->where('author_id', $user->id);
        }

        return $query;
    }

    /**
     * @param Domain $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            return in_array($object->id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate($args)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        $args['author_id'] = $user->id;

        return $args;
    }

    /**
     * @param Domain $object
     * @param array  $input_data
     *
     * @return array|void
     */
    public function onAfterCreate($object, $input_data)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $user->domains()->attach($object->id, [
                'flags' => UserDomainFlags::AFFILIATE_DOMAIN,
            ]);
        } else {             // allow super user to create domains in place of other users
            if (isset($input_data['target_user_id']) && !is_null($input_data['target_user_id'])) {
                $targetUser = call_user_func([config('larapress.crud.user.class'), 'find'], $input_data['target_user_id']);
                if (!is_null($targetUser)) {
                    $targetUser->domains()->attach(
                        $object->id,
                        [
                        'flags' => UserDomainFlags::AFFILIATE_DOMAIN,
                        ]
                    );
                }
            }
        }

        if (!empty($input_data['sub_domains'])) {
            $this->saveHasManyRelation('sub_domains', $object, $input_data, DomainSub::class);
        }

        $user->forgetDomainsCache();
    }

    /**
     * @param Domain $object
     * @param array $input_data
     *
     * @return array|void
     */
    public function onAfterUpdate($object, $input_data)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        // allow super user to create domains in place of other users
        if ($user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if (isset($input_data['target_user_id']) && !is_null($input_data['target_user_id'])) {
                $targetUser = call_user_func([config('larapress.crud.user.class'), 'find'], $input_data['target_user_id']);
                if (!is_null($targetUser)) {
                    $targetUser->domains()->attach(
                        $object->id,
                        [
                            'flags' => UserDomainFlags::AFFILIATE_DOMAIN,
                        ]
                    );
                }
            }
        }

        if (!empty($input_data['sub_domains'])) {
            $object->sub_domains()->forceDelete();
            $this->saveHasManyRelation('sub_domains', $object, $input_data, DomainSub::class);
        }

        $user->forgetDomainsCache();
        Cache::tags(['domains'])->flush();
    }
}
