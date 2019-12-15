<?php

return [
	'controllers' => [

	],

	'permissions' => [
        \Larapress\Profiles\MetaData\UserMetaData::class,
        \Larapress\Profiles\MetaData\ActivityLogMetaData::class,
        \Larapress\Profiles\MetaData\ActivateCodeHistoryMetaData::class,
        \Larapress\Profiles\MetaData\ActivityLogMetaData::class,
        \Larapress\Profiles\MetaData\DomainMetaData::class,
        \Larapress\Profiles\MetaData\EmailAddressMetaData::class,
        \Larapress\Profiles\MetaData\PhoneNumberMetaData::class,
        \Larapress\Profiles\MetaData\RoleMetaData::class,
        \Larapress\Profiles\MetaData\SettingsMetaData::class,
        \Larapress\Profiles\MetaData\FilterMetaData::class,
	],

    'security' => [
        'roles' => [
            'super-role' => [
                'super-role'
            ],
            'affiliate' => [
                'affiliate',
                'master'
            ],
            'customer' => [
                'customer'
            ],
        ]
    ],

    'defaults' => [
        'date-filter-interval' => '-1y',
        'cache-ttl' => '1d'
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
        'roles' => [
            'name' => 'roles',
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
        ]
    ]
];