<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\CRUD\Services\CRUD\Traits\CRUDRelationSyncTrait;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\DomainSub;

class DomainCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;
    use CRUDRelationSyncTrait;

    public $name_in_config = 'larapress.profiles.routes.domains.name';
    public $model_in_config = 'larapress.profiles.routes.domains.model';
    public $compositions_in_config = 'larapress.profiles.routes.domains.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
    ];
    public $createValidations = [
        'domain' => 'required|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'sub_domains.*' => 'nullable|string|domain|' .
            // unique in domains table
            'unique:domains,domain,NULL,id,deleted_at,NULL|' .
            // unique in sub domains table
            'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL',
        'ips' => 'nullable|string|ip_list',
        'nameservers' => 'nullable|string',
        'flags' => 'nullable|numeric',
        'data' => 'nullable|json_object',
        // used by super-role account to create domains in place of other users
        'targetUserId' => 'nullable|exists:users,id',
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
        'deleted_at',
    ];

    /**
     * Exclude current id in name unique request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'domain' => 'required|string|domain|' .
                // unique in domains table
                'unique:domains,domain,NULL,id,deleted_at,NULL,'.$request->route('id').'|' .
                // unique in sub domains table
                'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL,'.$request->route('id'),
            'sub_domains.*' => 'nullable|string|domain|' .
                // unique in domains table
                'unique:domains,domain,NULL,id,deleted_at,NULL,'.$request->route('id').'|' .
                // unique in sub domains table
                'unique:domains_subs,sub_domain,NULL,id,deleted_at,NULL,'.$request->route('id'),
            'ips' => 'nullable|string|ip_list',
            'nameservers' => 'nullable|string',
            'flags' => 'nullable|numeric',
            'data' => 'nullable|json_object',
            // used by super-role account to create domains in place of other users
            'targetUserId' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations(): array
    {
        return [
            'author' => config('larapress.crud.user.provider'),
            'sub_domains' => config('larapress.profiles.routes.domains.provider'),
        ];
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery(Builder $query): Builder
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $query->orWhere('author_id', $user->id);
            $query->onWhereIn('id', $user->getAffiliateDomainIds());
        }

        return $query;
    }

    /**
     * @param Domain $object
     *
     * @return bool
     */
    public function onBeforeAccess($object): bool
    {
        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($object->id, $user->getAffiliateDomainIds());
        }

        return true;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate($args): array
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
     * @return void
     */
    public function onAfterCreate($object, array $input_data): void
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $user->domains()->attach($object->id, [
                'flags' => UserDomainFlags::AFFILIATE_DOMAIN,
            ]);
        } else {             // allow super user to create domains in place of other users
            if (isset($input_data['targetUserId']) && !is_null($input_data['targetUserId'])) {
                $targetUser = call_user_func([config('larapress.crud.user.model'), 'find'], $input_data['targetUserId']);
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
     * @return void
     */
    public function onAfterUpdate($object, array $input_data): void
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        // allow super user to create domains in place of other users
        if ($user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            if (isset($input_data['targetUserId']) && !is_null($input_data['targetUserId'])) {
                $targetUser = call_user_func([config('larapress.crud.user.model'), 'find'], $input_data['targetUserId']);
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
        Helpers::forgetCachedValues(['domains']);
    }
}
