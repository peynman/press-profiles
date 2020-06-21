<?php

namespace Larapress\Profiles\CRUD;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Models\Role;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\Profiles\IProfileUser;

class UserCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.emails.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public function getModelClass()
    {
        return config('larapress.crud.user.class');
    }

    public $createValidations = [
        'name' => 'required|string|min:4|max:190|unique:users,name|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password' => 'required|string|min:4|confirmed|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password_confirmation' => 'required',
        'roles' => 'required|objectIds:roles,id,id',
        'register_domain_id' => 'required|exists:domains,id',
        'member_domain_id' => 'required|exists:domains,id',
        'flags' => 'nullable|numeric',
    ];
    public $updateValidations = [
        'password' => 'nullable|string|min:4|confirmed|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password_confirmation' => 'required_with:password',
        'roles' => 'required|objectIds:roles,id,id',
        'register_domain_id' => 'required|exists:domains,id',
        'member_domain_id' => 'required|exists:domains,id',
        'flags' => 'nullable|numeric',
    ];

    public $validRelations = [
        'roles',
        'roles.permissions',
        'domains',
        'phones',
        'emails',
    ];
    public $validSortColumns = ['id', 'name', 'created_at', 'updated_at'];
    public $validFilters = [];
    public $defaultShowRelations = ['roles', 'phone_numbers', 'domains', 'emails'];
    public $autoSyncRelations = ['roles', 'segments'];
    public $excludeFromUpdate = ['name'];
    public $searchColumns = ['name'];
    public $filterFields = [
        'from' => 'after:created_at',
        'to' => 'before:created_at',
        'role' => 'has:roles',
        'segment' => 'has:segments',
        'phone_numbers' => 'has:phone_numbers,number',
        'emails' => 'has:emails,email',
        'domains' => 'has:domains,domain',
    ];
    public $autoCountRelations = [];

    public function getFilterDefaultValues()
    {
        return [
            'from' => Carbon::now()->sub(config('larapress.profiles.defaults.date-filter-interval'))
                                    ->format('Y-m-d'),
            'to' => Carbon::now()->format('Y-m-d'),
            'role' => null,
            'segment' => null,
            'register_domain' => null,
            'domain' => null,
        ];
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate($args)
    {
        $args['password'] = self::makePassword($args['password']);

        /** @var ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('bet.master.role_name'))) {
            $reqRoles = Helpers::getNormalizedObjectIds($args['roles']);
            $protectedRoles = config('security.roles.protect');
            $roles = [];
            foreach ($reqRoles as $role) {
                if (! in_array($role, $protectedRoles)) {
                    $roles[] = $role;
                }
            }

            $args['roles'] = $roles;
            $args['segments'] = null;
        } elseif ($user->hasRole(config('bet.affiliate.role_name'))) {
            $args['roles'] = [config('bet.customer.role_id')];
            $args['segments'] = null;
        } else {
            if (isset($args['roles'])) {
                $args['roles'] = Helpers::getNormalizedObjectIds($args['roles']);
            }
            if (isset($args['segments'])) {
                $args['segments'] = Helpers::getNormalizedObjectIds($args['segments']);
            }
        }
        if (is_null($args['flags'])) {
            unset($args['flags']);
        }

        return $args;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeUpdate($args)
    {
        if (! empty($args['password'])) {
            $args['password'] = self::makePassword($args['password']);
        } else {
            unset($args['password']);
        }

        /** @var IProfileUser|ICRUDUser $user */
        $user = Auth::user();
        /** @var IRoleRepository $roleRepo */
        $roleRepo = app(IRoleRepository::class);
        /** @var Role $userHighRole */
        $userHighRole = $roleRepo->getUserHighestRole($user);
        $reqRolesIds = Helpers::getNormalizedObjectIds($args['roles']);
        /** @var Role[] $reqRoles */
        $reqRoles = Role::query()->whereIn('id', $reqRolesIds)->get();
        $roles = [];
        foreach ($reqRoles as $role) {
            if ($role->priority <= $userHighRole->priority) {
                $roles[] = $role->id;
            }
        }
        if ($user->hasRole(config('larapress.profiles.security.roles.master'))) {
            $args['roles'] = $roles;
            if (isset($args['segments'])) {
                $args['segments'] = Helpers::getNormalizedObjectIds($args['segments']);
            }
        }

        if (empty($args['flags'])) {
            unset($args['flags']);
        }

        return $args;
    }

    /**
     * @param IProfileUser|ICRUDUser $object
     * @param array $args
     */
    public function onAfterUpdate($object, $args)
    {
        if (isset($args['roles'])) {
            $object->forgetPermissionsCache();
        }

        $object->forgetDomainsCache();
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var IProfileUser|ICRUDUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profile.security.roles.affiliate'))) {
            $query->whereHas('domains', function (Builder $q) use ($user) {
                $q->whereIn('id', $user->getAffiliateDomainIds());
            });
        }

        return $query;
    }

    /**
     * @param IProfileUser|ICRUDUser $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profile.security.roles.affiliate'))) {
            if (! in_array($object->getMembershipDomainId(), $user->getAffiliateDomainIds())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $password
     *
     * @return string
     */
    public static function makePassword($password)
    {
        return Hash::make($password);
    }
}
