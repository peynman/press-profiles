<?php

return [
    'permissions' => [
        \Larapress\Profiles\MetaData\UserMetaData::class,
        \Larapress\Profiles\MetaData\ActivityLogMetaData::class,
        \Larapress\Profiles\MetaData\ActivateCodeMetaData::class,
        \Larapress\Profiles\MetaData\ActivateCodeHistoryMetaData::class,
        \Larapress\Profiles\MetaData\ActivityLogMetaData::class,
        \Larapress\Profiles\MetaData\DomainMetaData::class,
        \Larapress\Profiles\MetaData\EmailAddressMetaData::class,
        \Larapress\Profiles\MetaData\PhoneNumberMetaData::class,
        \Larapress\Profiles\MetaData\SettingsMetaData::class,
        \Larapress\Profiles\MetaData\FilterMetaData::class,
    ],

    'controllers' => [
        'curd' => [
            \Larapress\Profiles\CRUDControllers\UserController::class,
            \Larapress\Profiles\CRUDControllers\ActivateCodeController::class,
            \Larapress\Profiles\CRUDControllers\ActivateCodeHistoryController::class,
            \Larapress\Profiles\CRUDControllers\ActivityLogController::class,
            \Larapress\Profiles\CRUDControllers\DomainController::class,
            \Larapress\Profiles\CRUDControllers\EmailAddressController::class,
            \Larapress\Profiles\CRUDControllers\PhoneNumberController::class,
            \Larapress\Profiles\CRUDControllers\SettingsController::class,
            \Larapress\Profiles\CRUDControllers\FilterController::class,
        ],
        'crud-render' => [
            \Larapress\Profiles\CRUDRenderControllers\UserRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\ActivateCodeRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\ActivateCodeHistoryRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\ActivityLogRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\DomainRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\EmailAddressRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\PhoneNumberRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\SettingsRenderController::class,
            \Larapress\Profiles\CRUDRenderControllers\FilterRenderController::class,
        ]
    ],


    'security' => [
        'roles' => [
            'super-role' => [
                'super-role',
            ],
            'affiliate' => [
                'affiliate',
                'master',
            ],
            'customer' => [
                'customer',
            ],
        ],
    ],

    'defaults' => [
        'date-filter-interval' => '-1y',
        'cache-ttl' => '1d',
    ],

    'routes' => [
        'users' => [
            'name' => 'users',
        ],
        'user-affiliates' => [
            'name' => 'user-affiliates',
        ],
        'settings' => [
            'name' => 'settings',
        ],
        'phone-numbers' => [
            'name' => 'phone-numbers',
        ],
        'filters' => [
            'name' => 'filters',
        ],
        'emails' => [
            'name' => 'emails',
        ],
        'domains' => [
            'name' => 'domains',
        ],
        'activate-codes' => [
            'name' => 'activate-codes',
        ],
        'activate-codes-history' => [
            'name' => 'activate-codes-history',
        ],
        'activity-logs' => [
            'name' => 'activity-logs',
        ],
    ],
];
