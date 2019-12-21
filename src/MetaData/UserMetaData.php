<?php

namespace Larapress\Profiles\MetaData;

use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\Base\BasePermissionMetaData;
use Larapress\CRUD\Base\ICRUDFilterStorage;
use Larapress\CRUD\Base\IPermissionsMetaData;
use Larapress\CRUD\Base\SingleSourceBaseMetaData;
use Larapress\CRUD\ICRUDUser;
use Larapress\CRUDRender\Base\BaseCRUDPermissionView;
use Larapress\CRUDRender\Form\BaseCRUDFormMetaData;
use Larapress\CRUDRender\Form\FormField;
use Larapress\CRUDRender\Form\ICRUDFormMetaData;
use Larapress\CRUDRender\Menu\IMenuItemMetaData;
use Larapress\CRUDRender\Metrics\IMetricsReportMetaData;
use Larapress\CRUDRender\Metrics\MetricReportMetaData;
use Larapress\CRUDRender\Table\ITableViewMetaData;
use Larapress\CRUDRender\Table\TableViewColumn;
use Larapress\CRUDRender\Table\TableViewMetaData;
use Larapress\Profiles\Base\MetricHelpers;
use Larapress\Profiles\CRUD\UserCRUDProvider;
use Larapress\Profiles\Flags\UserFlags;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\CRUD\Models\Role;

class UserMetaData extends SingleSourceBaseMetaData implements
    IPermissionsMetaData,
    IMenuItemMetaData,
    ITableViewMetaData,
    ICRUDFormMetaData,
    IMetricsReportMetaData
{
    use BasePermissionMetaData;
    use BaseCRUDPermissionView;
    use TableViewMetaData;
    use BaseCRUDFormMetaData;
    use MetricReportMetaData;

    public function getPermissionVerbs()
    {
        return [
            self::VIEW,
            self::EDIT,
            self::DELETE,
            self::CREATE,
        ];
    }

    public function getPermissionObjectName()
    {
        return config('larapress.profiles.routes.users.name');
    }

    public function getMenuTitle()
    {
        return trans('sidebar.title.users');
    }

    public function singular()
    {
        return trans('models.user.name.singular');
    }

    public function plural()
    {
        return trans('models.user.name.plural');
    }

    public function getMenuIcon()
    {
        return '';
    }

    public function getMenuURL()
    {
        return $this->viewUrl();
    }

    public function getMenuKey()
    {
        return config('larapress.profiles.routes.users.name');
    }

    public function getMenuViewPermissions()
    {
        return [$this->getViewPermission()];
    }

    public function getMenuViewRoles()
    {
        return [];
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.users.name');
    }

    public function getTableColumns()
    {
        return [
            TableViewColumn::id(),
            TableViewColumn::column(trans('tables.column.username'), 'name'),
            TableViewColumn::filter(trans('tables.column.roles'), 'roles', 'JSBridge.getBadge(data)'),
            TableViewColumn::options(),
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
        $values = $filter->getFilterValues(session()->getId(), new UserCRUDProvider());

        return [
            FormField::common('date', 'from', trans('forms.label.from'), ['value' => $values['from']]),
            FormField::common('date', 'to', trans('forms.label.to'), ['value' => $values['to']]),
            FormField::common(
                'select',
                'role',
                trans('forms.label.roles'),
                [
                    'objects' => Role::all(['id', 'title']),
                    'value' => $values['role'],
                    'max-options' => 1,
                    'multiple' => true,
                ]
            ),
            FormField::common(
                'select',
                'domains',
                trans('forms.label.domains'),
                [
                    'objects' => Domain::all(['id', 'title']),
                    'value' => $values['domain'],
                    'max-options' => 1, 'multiple' => true,
                ]
            ),
            FormField::applyFilter(),
            FormField::removeFilter(),
        ];
    }

    public function queryParams()
    {
        return [
            'with' => [
                'roles' => [],
                'domains' => [],
            ],
        ];
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.users.name');
    }

    public function getCreateFields()
    {
        $roles = Role::query()->select(['id', 'title']);

        /** @var ICRUDUser|IProfileUser $user */
        $user = Auth::user();
        /** @var Role $userHighRole */
        $userHighRole = $user->roles()->orderBy('priority', 'DESC')->first();

        if ($user->hasRole(config('larapress.profiles.security.roles.master'))) {
            $roles->where('priority', '<=', $userHighRole->priority);
        }

        $fields = [
            FormField::text('name', trans('forms.label.name'), trans('forms.placeholders.username')),
            FormField::password('password', trans('forms.label.password'), trans('forms.placeholders.password')),
            FormField::password(
                'password_confirmation',
                trans('forms.label.password_confirm'),
                trans('forms.placeholders.confirm_password')
            ),
            FormField::objectGroup(
                'roles',
                trans('forms.label.roles'),
                $roles->get(),
                FormField::decorator('id', 'title', null, null, 'names'),
                []
            ),
            FormField::common('select', 'domain_id', trans('forms.label.domain'), [
                'objects' => Domain::all(['id', 'title']),
            ]),
            FormField::common('switches', 'flags', trans('forms.label.options'), [
                'objects' => UserFlags::toArray(),
                'decorator' => FormField::decorator('id', 'title'),
                'value' => 0,
            ]),
        ];

        if ($user->hasRole(config('bet.affiliate.role_name'))) {
            unset($fields[3]);
            unset($fields[4]);
            unset($fields[5]);
            unset($fields[6]);
        }

        return $fields;
    }

    /**
     * @param IProfileUser $object
     * @return array
     */
    public function getUpdateFields($object = null)
    {
        $roles = Role::query()->select(['id', 'title']);

        /** @var IProfileUser|ICRUDUser $user */
        $user = Auth::user();
        /** @var Role $userHighRole */
        $userHighRole = $user->roles()->orderBy('priority', 'DESC')->first();

        if ($user->hasRole(config('larapress.profiles.security.roles.master'))) {
            $roles->where('priority', '<=', $userHighRole->priority);
        }

        $fields = [
            FormField::text(
                'name',
                trans('forms.label.name'),
                trans('forms.placeholders.username'),
                null,
                'text',
                true
            ),
            FormField::password('password', trans('forms.label.password'), trans('forms.placeholders.password')),
            FormField::password(
                'password_confirmation',
                trans('forms.label.password_confirm'),
                trans('forms.placeholders.confirm_password')
            ),
            FormField::objectGroup(
                'roles',
                trans('forms.label.roles'),
                $roles->get(),
                FormField::decorator('id', 'title', null, null, 'names'),
                []
            ),
            FormField::objectGroup(
                'domains',
                trans('forms.label.domain'),
                Domain::all(['id', 'title']),
                FormField::decorator('id', 'title', null, null, 'names'),
                []
            ),
            FormField::common('switches', 'flags', trans('forms.label.options'), [
                'objects' => UserFlags::toArray(),
                'decorator' => FormField::decorator('id', 'title'),
                'value' => $object->flags,
            ]),
        ];

        if ($user->hasRole(config('bet.affiliate.role_name'))) {
            unset($fields[3]);
            unset($fields[4]);
            unset($fields[5]);
            unset($fields[6]);
        }

        return $fields;
    }

    public function getReportMetrics()
    {
        return [
            FormField::widget(
                'widget-metrics-chartjs-panel',
                'total_registered_users',
                ['label' => 'Total Registered Users'],
                [
                    'date-ranges' => array_merge(
                        MetricHelpers::getHoursDateRanges(),
                        MetricHelpers::getDaysDateRanges(),
                        MetricHelpers::getWeekMonthDateRanges()
                    ),
                    'classes.class' => 'col-md-4',
                    'chart' => [
                        'mode' => 'time-series',
                        'type' => 'bar',
                        'size' => [200, 100],
                        'queries' => [
                            [
                                'summarize' => 'Total Registered Users',
                                'where' => [
                                    [
                                        'operator' => 'LIKE',
                                        'phrase' => 'app.domain.any.user.registered',
                                    ],
                                ],
                            ],
                        ],
                        'legend_labels' => [
                            null => 'Registered Users',
                        ],
                    ],
                ]
            ),
            FormField::widget(
                'widget-metrics-chartjs-panel',
                'total_login',
                ['label' => 'Total Logged In'],
                [
                    'date-ranges' => array_merge(
                        MetricHelpers::getHoursDateRanges(),
                        MetricHelpers::getDaysDateRanges(),
                        MetricHelpers::getWeekMonthDateRanges()
                    ),
                    'classes.class' => 'col-md-4',
                    'chart' => [
                        'mode' => 'time-series',
                        'type' => 'bar',
                        'size' => [200, 100],
                        'queries' => [
                            [
                                'summarize' => 'Total Logged In',
                                'where' => [
                                    [
                                        'operator' => 'LIKE',
                                        'phrase' => 'app.domain.any.user.login',
                                    ],
                                ],
                            ],
                        ],
                        'legend_labels' => [
                            null => 'Logged In Users',
                        ],
                    ],
                ]
            ),
            FormField::widget(
                'widget-metrics-chartjs-panel',
                'registered_domains',
                ['label' => 'User Registration Domain Market Share'],
                [
                    'date-ranges' => array_merge(
                        MetricHelpers::getHoursDateRanges(),
                        MetricHelpers::getDaysDateRanges(),
                        MetricHelpers::getWeekMonthDateRanges()
                    ),
                    'classes.class' => 'col-md-4',
                    'chart' => [
                        'type' => 'pie',
                        'mode' => 'pie',
                        'size' => [100, 100],
                        'queries' => [
                            [
                                'where' => [
                                    [
                                        'operator' => 'LIKE',
                                        'phrase' => 'app.domain.%.user.registered',
                                    ],
                                    [
                                        'operator' => 'NOT LIKE',
                                        'phrase' => 'app.domain.any.user.registered',
                                    ],
                                ],
                            ],
                        ],
                        'legend_labels_functions' => MetricHelpers::getSubDomainLabelFunctions(
                            'str_contains',
                            function ($d) {
                                return str_replace('.', '_', $d);
                            }
                        ),
                    ],
                ]
            ),
        ];
    }
}
