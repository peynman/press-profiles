<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Larapress\CRUD\Services\CRUD\BaseCRUDProvider;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Services\RBAC\IPermissionsMetadata;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\Repository\Domain\IDomainRepository;

/**
 * User CRUD rules and features
 */
class UserCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.users.name';
    public $extend_in_config = 'larapress.profiles.routes.users.extend.providers';

    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::REPORTS,
    ];

    /**
     * @bodyParam name string required The username to use for the new user. Example: user23124
     * @bodyParam password string required The password to use for the new user. Example: somepassworDS123
     * @bodyParam phones object[] A list of phone numbers to attach to user.
     * @bodyParam phones[].number string required The new number to attach to user. Example: 98912132456432
     * @bodyParam phones[].flags int required Default flags to set on created phone number resource. Example: 0
     * @bodyParam domains object[] A list of domains to attach to user.
     * @bodyParam domains[].id int required The id of the domain to attach. Example: 1
     * @bodyParam domains[].flags int required Default flags to set on users relation to the domain. Example: 0
     */
    public $createValidations = [
        'name' => 'required|string|min:4|max:190|unique:users,name|regex:/(^[A-Za-z0-9-_.]+$)+/',
        'password' => 'required|string|min:4|confirmed',
        'password_confirmation' => 'required',
        'roles' => 'required|array',
        'domains' => 'required|array',
        'phones' => 'nullable|array',
        'emails' => 'nullable|array',
        'roles.*.id' => 'required|exists:roles,id',
        'domains.*.id' => 'required|exists:domains,id',
        'phones.*.number' => 'nullable|numeric|regex:/(09)[0-9]{9}/',
        'emails.*.email' => 'nullable|email',
        'flags' => 'nullable|numeric',
    ];

    /**
     * Undocumented variable
     *
     * @bodyParam id int Sort based on id
     *
     */
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
        'domains'
    ];
    public $searchColumns = [
        'equals:name',
        'has:phones,number',
    ];
    public $filterFields = [
        'created_from' => 'after:created_at',
        'created_to' => 'before:created_at',
        'roles' => 'has:roles',
        'phones' => 'has:phones:number',
        'domains' => 'has:domains',
        'year' => 'has:profile:data->values->year',
        'field' => 'has:profile:data->values->field',
    ];

    public function getModelClass()
    {
        return config('larapress.crud.user.class');
    }

    /**
     * @param Request $request
     *
     *
     *
     * @return array
     */
    public function getUpdateRules(Request $request)
    {
        $updateValidations = [
            'name' => 'nullable|string|min:4|max:190|regex:/(^[A-Za-z0-9-_.]+$)+/|unique:users,name',
            'password' => 'nullable|string|min:4|confirmed',
            'password_confirmation' => 'required_with:password',
            'roles' => 'required|array',
            'domains' => 'required|array',
            'phones' => 'nullable|array',
            'emails' => 'nullable|array',
            'roles.*.id' => 'required|exists:roles,id',
            'domains.*.id' => 'required|exists:domains,id',
            'phones.*.number' => 'nullable|numeric|regex:/(09)[0-9]{9}/',
            'emails.*.email' => 'nullable|email',
            'flags' => 'nullable|numeric',
        ];
        $updateValidations['name'] .= ',' . $request->route('id') . ',id';
        return $updateValidations;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getValidRelations()
    {
        return [
            'roles' => function ($user) {
                return $user->hasPermission(config('larapress.crud.routes.roles.name') . '.view');
            },
            'roles.permissions' => function ($user) {
                return $user->hasPermission(config('larapress.crud.routes.roles.name') . '.view');
            },
            'domains' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.domains.name') . '.view');
            },
            'phones' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.phone-numbers.name') . '.view');
            },
            'emails' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.emails.name') . '.view');
            },
            'form_support_user_profile' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name') . '.view');
            },
            'form_profile_default' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name') . '.view');
            },
            'form_profile_support' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name') . '.view');
            },
            'form_support_registration_entry' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.form-entries.name') . '.view');
            },
            'wallet_balance' => function ($user) {
                return $user->hasPermission(config('larapress.ecommerce.routes.wallet-transactions.name') . '.view');
            },
            'sales_fixed' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name') . '.sales');
            },
            'sales_periodic' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name') . '.sales');
            },
            'sales_real' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name') . '.sales');
            },
            'sales_virtual' => function ($user) {
                return $user->hasPermission(config('larapress.profiles.routes.users.name') . '.sales');
            },
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

        if (isset($args['flags']) && is_null($args['flags'])) {
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
        Cache::tags(['user.domains:' . $object->id])->flush();
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

        if (isset($args['flags']) && is_null($args['flags'])) {
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
        Cache::tags(['user.roles:' . $object->id])->flush();
        Cache::tags(['user.domains:' . $object->id])->flush();
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if ($user->hasRole(config('larapress.lcms.support_role_id'))) {
                $query->whereHas('form_entries', function ($q) use ($user) {
                    $q->where('tags', 'support-group-' . $user->id);
                });
            } elseif (!$user->hasRole(config('larapress.lcms.owner_role_id'))) {
                $query->whereHas('domains', function (Builder $q) use ($user) {
                    $q->whereIn('id', $user->getAffiliateDomainIds());
                });
            }
        }

        return $query;
    }

    /**
     * @param IProfileUser $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if (!in_array($object->getMembershipDomainId(), $user->getAffiliateDomainIds())) {
                return false;
            }
        }

        return true;
    }
}
