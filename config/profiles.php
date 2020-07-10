<?php

return [
    'defaults' => [
        'date-filter-interval' => '-1y',
        'cache-ttl' => '1d',
        'profile-form-id' => 1
    ],

    'translations' => [
        'namespace' => 'larapress'
    ],

    'permissions' => [
        \Larapress\Profiles\CRUD\UserCRUDProvider::class,
        \Larapress\Profiles\CRUD\ActivityLogCRUDProvider::class,
        \Larapress\Profiles\CRUD\DomainCRUDProvider::class,
        \Larapress\Profiles\CRUD\EmailAddressCRUDProvider::class,
        \Larapress\Profiles\CRUD\PhoneNumberCRUDProvider::class,
        \Larapress\Profiles\CRUD\SettingsCRUDProvider::class,
        \Larapress\Profiles\CRUD\FilterCRUDProvider::class,
        \Larapress\Profiles\CRUD\FormCRUDProvider::class,
        \Larapress\Profiles\CRUD\FormEntryCRUDProvider::class,
    ],

    'controllers' => [
        \Larapress\Profiles\CRUDControllers\UserController::class,
        \Larapress\Profiles\CRUDControllers\ActivityLogController::class,
        \Larapress\Profiles\CRUDControllers\DomainController::class,
        \Larapress\Profiles\CRUDControllers\EmailAddressController::class,
        \Larapress\Profiles\CRUDControllers\PhoneNumberController::class,
        \Larapress\Profiles\CRUDControllers\SettingsController::class,
        \Larapress\Profiles\CRUDControllers\FilterController::class,
        \Larapress\Profiles\CRUDControllers\FormController::class,
        \Larapress\Profiles\CRUDControllers\FormEntryController::class,
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
        'devices' => [
            'name' => 'devices',
        ],
        'forms' => [
            'name' => 'forms',
        ],
        'form-entries' => [
            'name' => 'form-entries'
        ],
        'activity-logs' => [
            'name' => 'activity-logs',
        ],
    ],
];
