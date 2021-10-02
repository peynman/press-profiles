<?php

namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Larapress\CRUD\Services\CRUD\Traits\CRUDProviderTrait;
use Larapress\CRUD\Services\CRUD\ICRUDProvider;
use Larapress\CRUD\Exceptions\AppException;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\CRUD\Services\CRUD\ICRUDVerb;
use Larapress\CRUD\Services\CRUD\Traits\CRUDRelationSyncTrait;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\PhoneNumber;
use Larapress\Profiles\Repository\Domain\IDomainRepository;

/**
 * User CRUD rules and features
 */
class UserCRUDProvider implements ICRUDProvider
{
    use CRUDProviderTrait;
    use CRUDRelationSyncTrait;

    public $name_in_config = 'larapress.profiles.routes.users.name';
    public $model_in_config = 'larapress.crud.user.model';
    public $compositions_in_config = 'larapress.crud.user.compositions';

    public $verbs = [
        ICRUDVerb::VIEW,
        ICRUDVerb::SHOW,
        ICRUDVerb::CREATE,
        ICRUDVerb::EDIT,
        ICRUDVerb::DELETE,
        ICRUDVerb::REPORTS,
        ICRUDVerb::EXPORT,
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
        'roles.*' => 'required|exists:roles,id',
        'domains.*.id' => 'required|exists:domains,id',
        'phones.*.number' => 'nullable|numeric|regex:/(09)[0-9]{9}/',
        'emails.*.email' => 'nullable|email',
        'addresses.*.address' => 'nullable|string',
        'addresses.*.postal_code' => 'nullable|string',
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
    public $searchColumns = [
        'equals:name',
        'has:phones,number',
    ];
    public $filterFields = [
        'created_from' => 'after:created_at',
        'created_to' => 'before:created_at',
        'updated_from' => 'after:upated_at',
        'updated_to' => 'before:updated_at',
        'roles' => 'has:roles',
        'phones' => 'has:phones:number',
        'domains' => 'has:domains',
    ];
    public $defaultShowRelations = [
        'form_profile_default',
        'roles',
        'domains',
        'emails',
        'addresses',
        'phones',
    ];

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return config('larapress.crud.user.model');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
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
            'addresses.*.address' => 'nullable|string',
            'addresses.*.postal_code' => 'nullable|string',
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
    public function getValidRelations(): array
    {
        return [
            'roles' => config('larapress.crud.routes.roles.provider'),
            'domains' => config('larapress.profiles.routes.domains.provider'),
            'phones' => config('larapress.profiles.routes.phone_numbers.provider'),
            'emails' => config('larapress.profiles.routes.emails.provider'),
            'addresses' => config('larapress.profiles.routes.addresses.provider'),
            'form_profile_default' => config('larapress.profiles.routes.form_entries.provider'),
        ];
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeCreate(array $args): array
    {
        $args['password'] = Hash::make($args['password']);

        return $args;
    }

    /**
     * @param IProfileUser $object
     * @param array $args
     *
     * @return void
     */
    public function onAfterCreate($object, array $args): void
    {
        $this->syncRoles($object, $args);
        $this->syncDomains($object, $args);

        if (isset($args['addresses'])) {
            $this->syncAddresses($object, $args);
        }

        if (isset($args['emails'])) {
            $this->syncEmails($object, $args);
        }

        if (isset($args['phones'])) {
            $this->syncPhones($object, $args['phones']);
        }

        Helpers::forgetCachedValues([
            'user:' . $object->id,
            'user.domains:' . $object->id
        ]);
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function onBeforeUpdate(array $args): array
    {
        if (!empty($args['password'])) {
            $args['password'] = Hash::make($args['password']);
        } else {
            unset($args['password']);
        }

        if (isset($args['flags']) && is_null($args['flags'])) {
            unset($args['flags']);
        }

        return $args;
    }

    /**
     * @param IProfileUser $object
     * @param array $args
     *
     * @return void
     */
    public function onAfterUpdate($object, $args): void
    {
        $this->syncRoles($object, $args);
        $this->syncDomains($object, $args);

        if (isset($args['addresses'])) {
            $this->syncAddresses($object, $args);
        }

        if (isset($args['emails'])) {
            $this->syncEmails($object, $args);
        }

        if (isset($args['phones'])) {
            $this->syncPhones($object, $args['phones']);
        }

        Helpers::forgetCachedValues([
            'user:' . $object->id,
            'user.roles:' . $object->id,
            'user.domains:' . $object->id,
        ]);
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
            if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
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
    public function onBeforeAccess($object): bool
    {
        /** @var IProfileUser $user */
        $user = Auth::user();

        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            return in_array($object->getMembershipDomainId(), $user->getAffiliateDomainIds());
        }

        return true;
    }

    /*
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array[] $args
     * @return void
     */
    public function syncRoles($user, $args)
    {
        /** @var IRoleRepository */
        $repo = app(IRoleRepository::class);
        /** @var ICRUDUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $this->syncBelongsToManyRelation('roles', $user, $args);
        } else {
            $validRoles = $repo->getVisibleRoles($user);
            $this->syncBelongsToManyRelation('roles', $user, $args, function ($arg) use ($validRoles) {
                foreach ($validRoles as $validRole) {
                    if ($validRole->id === $arg) {
                        return true;
                    }
                }
                return false;
            });
        }
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array $args
     *
     * @return void
     */
    public function syncDomains($user, $args)
    {
        // sync domains with their attributes in pivot tables
        $this->syncBelongsToManyRelation('domains', $user, $args, null, function ($arg) {
            return [
                'flags' => isset($arg['pivot']['flags']) ? $arg['pivot']['flags'] : 0,
            ];
        }, function ($arg) {
            return $arg['id'];
        });
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array[] $phones
     * @return void
     */
    public function syncPhones($user, $phones)
    {
        /** @var IDomainRepository */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getCurrentRequestDomain();
        foreach ($phones as $phone) {
            $dbPhone = null;
            if (isset($phone['id'])) {
                $dbPhone = PhoneNumber::find($phone['id']);
            } else {
                $dbPhone = PhoneNumber::query()
                    ->where('user_id', $user->id)
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
                        'user_id' => $user->id,
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

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array $args
     * @return void
     */
    public function syncAddresses($user, $args)
    {
    }

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array $args
     * @return void
     */
    public function syncEmails($user, $args)
    {
    }
}
