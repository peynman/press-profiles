<?php

namespace Larapress\Profiles\CRUD;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Services\BaseCRUDProvider;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\CRUD\Services\IPermissionsMetadata;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Exceptions\ValidationException;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Models\Role;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\Repository\Domain\IDomainRepository;

class UserCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.users.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];
    public function getModelClass()
    {
        return config('larapress.crud.user.class');
    }

    public $createValidations = [
        'name' => 'required|string|min:4|max:190|unique:users,name|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password' => 'required|string|min:4|confirmed|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password_confirmation' => 'required',
        'roles.*.id' => 'required|exists:roles,id',
        'domains.*.id' => 'required|exists:domains,id',
        'phones.*.number' => 'nullable|numeric|regex:/(09)[0-9]{9}/',
        'flags' => 'nullable|numeric',
    ];
    public $updateValidations = [
        'name' => 'required|string|min:4|max:190|regex:/(^[A-Za-z0-9-_.]+$)+/|unique:users,name',
        'password' => 'nullable|string|min:4|confirmed|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password_confirmation' => 'required_with:password',
        'roles.*.id' => 'required|exists:roles,id',
        'domains.*.id' => 'required|exists:domains,id',
        'phones.*.number' => 'nullable|numeric|regex:/(09)[0-9]{9}/',
        'flags' => 'nullable|numeric',
    ];

    public $validRelations = [
        'roles',
        'roles.permissions',
        'domains',
        'phones',
        'emails',
        'profile',
        'supportUserProfile',
    ];
    public $validSortColumns = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];
    public $defaultShowRelations = [
        'roles',
        'phones',
        'emails',
        'domains',
    ];
    public $searchColumns = [
        'name',
        'has:phones,number',
    ];
    public $filterFields = [
        'roles' => 'has:roles',
        'phones' => 'has:phones:number',
        'domains' => 'has:domains',
        'year' => 'has:profile:data->values->year',
        'field' => 'has:profile:data->values->field',
    ];
    public $autoCountRelations = [];

    /**
     * Exclude current id in name unique request
     *
     * @param Request $request
     * @return void
     */
    public function getUpdateRules(Request $request)
    {
        $this->updateValidations['name'] .= ',' . $request->route('id');
        return $this->updateValidations;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getFilterDefaultValues()
    {
        return [
            'from' => Carbon::now()->sub(config('larapress.profiles.defaults.date-filter-interval'))->format('Y-m-d'),
            'to' => Carbon::now()->format('Y-m-d'),
            'role' => null,
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
        $args['password'] = Hash::make($args['password']);

        return $args;
    }

    /**
     * @param IProfileUser|ICRUDUser $object
     * @param array $args
     */
    public function onAfterCreate($object, $args)
    {
        /** @var IRoleRepository */
        $repo = app(IRoleRepository::class);

        /** @var ICRUDUser $user */
        $user = Auth::user();

        if ($user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            // sync for super role with no limit
            $this->syncBelongsToManyRelation('roles', $object, $args);
        } else {
            // sync roles in visible roles to user only
            $validRoles = $repo->getVisibleRoles($user);
            $this->syncBelongsToManyRelation('roles', $object, $args, function ($arg) use ($validRoles) {
                // return true if role in $arg can be included in users sync
                foreach ($validRoles as $validRole) {
                    if ($validRole->id === $arg['id']) {
                        return true;
                    }
                }
                return false;
            });
        }

        // sync domains with their attributes in pivot tables
        $this->syncBelongsToManyRelation('domains', $object, $args, null, function ($arg) {
            return [
                'flags' => isset($arg['pivot']['flags']) ? $arg['pivot']['flags'] : 0,
            ];
        });

        if (is_null($args['flags'])) {
            unset($args['flags']);
        }

        if (isset($args['phones'])) {
            /** @var IDomainRepository */
            $domainRepo = app(IDomainRepository::class);
            $domain = $domainRepo->getCurrentRequestDomain();
            foreach ($args['phones'] as $phone) {
                $dbPhone = null;
                if (isset($phone['id'])) {
                    $dbPhone = PhoneNumber::find($phone['id']);
                } else {
                    $dbPhone = PhoneNumber::query()
                        ->where('user_id', $object->id)
                        ->where('domain_id', $domain->id)
                        ->where('number', $phone['number'])
                        ->first();
                }
                if (is_null($dbPhone)) {
                    // check for same number in this domain;
                    //   dont create a new phone if someone in the same domain has this phone
                    $sameNumber = PhoneNumber::query()
                        ->where('number', $phone['number'])
                        ->where('domain_id', $domain->id)
                        ->first();
                    if (!is_null($sameNumber)) {
                        if (is_null($sameNumber->user_id)) {
                            $sameNumber->update([
                                'user_id' => $object->id,
                                'flags' => isset($phone['flags']) && !is_null($phone['flags']) ? $phone['flags'] : 0,
                            ]);
                        } else {
                            throw new AppException(AppException::ERR_NUMBER_ALREADY_EXISTS);
                        }
                    } else {
                        $dbPhone = PhoneNumber::create([
                            'number' => $phone['number'],
                            'flags' => isset($phone['flags']) && !is_null($phone['flags']) ? $phone['flags'] : 0,
                            'user_id' => $object->id,
                            'domain_id' => $domain->id,
                        ]);
                    }
                } else {
                    $dbPhone->update([
                        'flags' => isset($phone['flags']) && !is_null($phone['flags']) ? $phone['flags'] : 0,
                    ]);
                }
            }
        }

        Cache::tags(['user:' . $object->id])->flush();
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeUpdate($args)
    {
        if (!empty($args['password'])) {
            $args['password'] = Hash::make($args['password']);
        } else {
            unset($args['password']);
        }

        return $args;
    }

    /**
     * @param IProfileUser|ICRUDUser $object
     * @param array $args
     */
    public function onAfterUpdate($object, $args)
    {
        /** @var IRoleRepository */
        $repo = app(IRoleRepository::class);

        /** @var ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $this->syncBelongsToManyRelation('roles', $object, $args);
        } else {
            $validRoles = $repo->getVisibleRoles($user);
            $this->syncBelongsToManyRelation('roles', $object, $args, function ($arg) use ($validRoles) {
                foreach ($validRoles as $validRole) {
                    if ($validRole->id === $arg['id']) {
                        return true;
                    }
                }
                return false;
            });
        }
        // sync domains with their attributes in pivot tables
        $this->syncBelongsToManyRelation('domains', $object, $args, null, function ($arg) {
            return [
                'flags' => isset($arg['pivot']['flags']) ? $arg['pivot']['flags'] : 0,
            ];
        });

        if (is_null($args['flags'])) {
            unset($args['flags']);
        }

        if (isset($args['phones'])) {
            /** @var IDomainRepository */
            $domainRepo = app(IDomainRepository::class);
            $domain = $domainRepo->getCurrentRequestDomain();
            foreach ($args['phones'] as $phone) {
                $dbPhone = null;
                if (isset($phone['id'])) {
                    $dbPhone = PhoneNumber::find($phone['id']);
                } else {
                    $dbPhone = PhoneNumber::query()
                        ->where('user_id', $object->id)
                        ->where('domain_id', $domain->id)
                        ->where('number', $phone['number'])
                        ->first();
                }
                if (is_null($dbPhone)) {
                    // check for same number in this domain;
                    //   dont create a new phone if someone in the same domain has this phone
                    $sameNumbers = PhoneNumber::query()
                        ->where('number', $phone['number'])
                        ->where('domain_id', $domain->id)
                        ->count();
                    if ($sameNumbers > 0) {
                        throw new AppException(AppException::ERR_NUMBER_ALREADY_EXISTS);
                    } else {
                        $dbPhone = PhoneNumber::create([
                            'number' => $phone['number'],
                            'flags' => isset($phone['flags']) && !is_null($phone['flags']) ? $phone['flags'] : 0,
                            'user_id' => $object->id,
                            'domain_id' => $domain->id,
                        ]);
                    }
                } else {
                    $dbPhone->update([
                        'number' => $phone['number'],
                        'flags' => isset($phone['flags']) && !is_null($phone['flags']) ? $phone['flags'] : 0,
                    ]);
                }
            }
        }

        Cache::tags(['user:' . $object->id])->flush();
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

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            $query->orWhereHas('domains', function (Builder $q) use ($user) {
                $q->whereIn('id', $user->getAffiliateDomainIds());
            });
            $query->orWhereHas('form_entries', function($q) use($user) {
                $q->where('tags', 'support-group-'.$user->id);
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

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if (!in_array($object->getMembershipDomainId(), $user->getAffiliateDomainIds())) {
                return false;
            }
        }

        return true;
    }
}
