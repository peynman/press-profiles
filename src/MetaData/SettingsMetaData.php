<?php



namespace Larapress\Profiles\MetaData;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\ICRUDFilterStorage;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUDRender\CRUD\BaseCRUDPermissionView;
use Larapress\CRUDRender\CRUD\ICRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Rendering\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Rendering\Table\ITableViewMetaData;
use Larapress\CRUDRender\Rendering\Table\TableViewColumn;
use Larapress\CRUDRender\Rendering\Table\TableViewMetaData;
use Larapress\Profiles\CRUD\SettingsCRUDProvider;
use Larapress\Profiles\Models\Domain;

class SettingsMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
    ICRUDPermissionView,
    IMenuItemMetaData,
    ITableViewMetaData,
    ICRUDFormMetaData
{
    use BasePermissionMetaData;
    use BaseCRUDPermissionView;
    use TableViewMetaData;
    use BaseCRUDFormMetaData;

    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.settings.name');
    }

    public function getPermissionVerbs()
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    public function title()
    {
        return trans('models.settings.sidebar');
    }
    public function singular()
    {
        return trans('models.settings.name.singular');
    }
    public function plural()
    {
        return trans('models.settings.name.plural');
    }

    public function key()
    {
        return config('larapress.profiles.routes.settings.name');
    }

    public function icon()
    {
        return '';
    }

    public function url()
    {
        return $this->viewUrl();
    }

    /**
     * @return array
     */
    public function viewPermissions()
    {
        return [$this->getViewPermission()];
    }
    public function viewRoles()
    {
        return [];
    }

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(
                trans('tables.column.domain'),
                'domain.domain',
                false,
                30,
                DomainMetaData::instance()->editUrl('::sub_domain_id:')
            ),
            TableViewColumn::column(trans('tables.column.type'), 'type'),
            TableViewColumn::column(
                trans('tables.column.username'),
                'user.name',
                false,
                30,
                UserMetaData::instance()->editUrl('::user_id:')
            ),
            TableViewColumn::column(trans('tables.column.key'), 'key'),
            TableViewColumn::column(trans('tables.column.content'), 'val', false, 15),
            TableViewColumn::options()
        ];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.settings.name');
    }

    public function queryParams()
    {
        return [
            'with' => [
                'user' => [],
                'sub_domain' => [],
            ]
        ];
    }


    public function hasCreate()
    {
        return false;
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.settings.name');
    }
    public function getCreateFields()
    {
        return [
            FormField::text('user_id', trans('forms.label.user_id')),
            FormField::text('key', trans('forms.label.key')),
            FormField::text('val', trans('forms.label.value')),
            FormField::text('type', trans('forms.label.type')),
            FormField::common('select', 'sub_domain_id', trans('forms.label.domain'), [
                'objects' => Domain::all(['id', 'title']),
            ]),
        ];
    }

    /**
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getFilterFields()
    {
        /** @var ICRUDFilterStorage $filter */
        $filter = app()->make(ICRUDFilterStorage::class);
        $values = $filter->getFilterValues(session()->getId(), new SettingsCRUDProvider());

        return [
            FormField::common('select', 'domain', trans('forms.label.domain'), [
                'objects' => Domain::select(['id', 'domain'])->get(),
                'decorator' => FormField::decorator('id', 'domain'),
                'value' => $values['sub_domain'],
                'multiple' => true,
                'max-options' => 1,
            ]),
            FormField::text('user_id', trans('forms.label.user_id'), null, null, 'text', false, null, null, [
                'value' => $values['user_id'],
            ]),
            FormField::text('type', trans('forms.label.type'), null, null, 'text', false, null, null, [
                'value' => $values['type'],
            ]),
            FormField::applyFilter(),
            FormField::removeFilter(),
        ];
    }

    public function getUpdateFields($object = null)
    {
        return [
            FormField::text('user_id', trans('forms.label.user_id')),
            FormField::text('key', trans('forms.label.key')),
            FormField::text('val', trans('forms.label.value')),
            FormField::text('type', trans('forms.label.type')),
            FormField::common('select', 'sub_domain_id', trans('forms.label.domain'), [
                'objects' => Domain::all(['id', 'title']),
            ]),
        ];
    }


    public function actions()
    {
        /** @var Domain[] $domains */
        $domains = null;
        /** @var \Larapress\CRUD\ICRUDUser|\Larapress\Profiles\IProfileUser $user */
        $user = Auth::user();
        if ($user->hasRole(config('bet.affiliate.role_name'))) {
            $domains = $user->getAffiliateDomains();
        } else {
            $domains = Domain::all(['id', 'title', 'domain']);
        }

        $dropdowns = [];
        foreach ($domains as $domain) {
            $dropdowns[] = [
                'title' => $domain->title. ' ('.$domain->domain.')',
                'icon' => '',
                'method' => 'POST',
                'link' => function (...$params) use ($domain) {
                    return route(
                        'settings.create.duplicate.domain.view.post',
                        [
                            'settings_id' => $params[0],
                            'domain_id' => $domain->id
                        ]
                    );
                },
            ];
        }

        return [
            [
                'title' => trans('forms.create_title', [
                    'target' => trans('forms.label.domain'),
                ]),
                'icon' => 'content-paste',
                'dropdown' => $dropdowns,
                'metadata' => SettingsMetaData::instance(),
            ],
        ];
    }
}
