<?php


namespace Larapress\Profiles\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BaseCRUDProvider;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\CRUD\Base\IPermissionsMetadata;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Filter;

class FilterCRUDProvider implements ICRUDProvider, IPermissionsMetadata
{
    use BaseCRUDProvider;

    public $name_in_config = 'larapress.profiles.routes.filters.name';
    public $verbs = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public $model = Filter::class;
    public $createValidations = [
        'title.default' => 'required|string|max:190',
        'title.translations.*' => 'nullable|string|max:190',
        'name' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'data' => 'nullable|json',
        'domains' => 'nullable|db_object_ids:domains,id,id',
    ];
    public $updateValidations = [
        'title.default' => 'required|string|max:190',
        'title.translations.*' => 'nullable|string|max:190',
        'name' => 'required|string|max:190',
        'type' => 'required|string|max:190',
        'data' => 'nullable|json',
        'domains' => 'nullable|db_object_ids:domains,id,id',
    ];
    public $translations = [
        'title'
    ];
    public $validSortColumns = [
        'id',
        'name',
        'type',
        'title',
        'author_id',
        'created_at',
        'updated_at',
    ];
    public $validRelations = [
        'author',
        'domains',
    ];
    public $defaultShowRelations = [
        'author',
        'domains',
    ];
    public $searchColumns = [
        'id' => 'equals:id',
        'name',
        'type',
        'translations',
    ];

    public function onBeforeCreate($args)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        $args['author_id'] = $user->id;

        return $args;
    }

    /**
     * @param Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function onBeforeQuery($query)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $query->has('domains', function(Builder $q) use($user) {
                $q->whereIn('id', $user->getAffiliateDomainIds());
            });
        }

        return $query;
    }

    /**
     * @param Filter $object
     *
     * @return bool
     */
    public function onBeforeAccess($object)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('larapress.profiles.security.roles.affiliate'))) {
            $domainFilters = $object->domains;
            foreach ($domainFilters as $domain) {
                if (in_array($domain->id, $user->getAffiliateDomainIds())) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }


    /**
     * @param Filter $object
     * @param array  $input_data
     *
     * @return array|void
     */
    public function onAfterCreate($object, $input_data)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        $object->domains()->detach($user->getAffiliateDomainIds());
        self::syncWithoutDetachingBelongsToManyRelation('domains', $object, $input_data);
    }

    /**
     * @param Filter $object
     * @param array $input_data
     * @return array|void
     */
    public function onAfterUpdate($object, $input_data)
    {
        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();

        $object->domains()->detach($user->getAffiliateDomainIds());
        self::syncWithoutDetachingBelongsToManyRelation('domains', $object, $input_data);
    }
}
