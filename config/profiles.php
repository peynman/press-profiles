<?php

return [
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
        \Larapress\Profiles\CRUD\DeviceCRUDProvider::class,
    ],

    'controllers' => [
        \Larapress\Profiles\Controllers\UserController::class,
        \Larapress\Profiles\Controllers\ActivityLogController::class,
        \Larapress\Profiles\Controllers\DomainController::class,
        \Larapress\Profiles\Controllers\EmailAddressController::class,
        \Larapress\Profiles\Controllers\PhoneNumberController::class,
        \Larapress\Profiles\Controllers\SettingsController::class,
        \Larapress\Profiles\Controllers\FilterController::class,
        \Larapress\Profiles\Controllers\FormController::class,
        \Larapress\Profiles\Controllers\FormEntryController::class,
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
