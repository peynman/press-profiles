<?php

return [
    // form id for customer profile
    'default_profile_form_id' => 1,

    // role based profile form id
    'form_role_profiles' => [
        'super_role' => 1,
    ],

    // role groups used in domain based access controls
    'security' => [
        'roles' => [
            // super role users can access any resource
            'super_role' => [
                'super-role',
            ],
            // affiliate users can access any resource in their owned domains
            'affiliate' => [
                'affiliate',
                'master',
            ],
            // customer roles can access their own resources only
            'customer' => [
                'customer',
            ],
        ],
    ],

    // crud routes implemented in Larapress Profiles
    'routes' => [
        'users' => [
            'name' => 'users',
            // model & provider for users is set in lararpess.crud.user config path
        ],
        'settings' => [
            'name' => 'settings',
            'model' => \Larapress\Profiles\Models\Settings::class,
            'provider' => \Larapress\Profiles\CRUD\SettingsCRUDProvider::class,
        ],
        'phone_numbers' => [
            'name' => 'phone-numbers',
            'model' => \Larapress\Profiles\Models\PhoneNumber::class,
            'provider' => \Larapress\Profiles\CRUD\PhoneNumberCRUDProvider::class,
        ],
        'filters' => [
            'name' => 'filters',
            'model' => \Larapress\Profiles\Models\Filter::class,
            'provider' => \Larapress\Profiles\CRUD\FilterCRUDProvider::class,
        ],
        'emails' => [
            'name' => 'emails',
            'model' => \Larapress\Profiles\Models\EmailAddress::class,
            'provider' => \Larapress\Profiles\CRUD\EmailAddressCRUDProvider::class,
        ],
        'addresses' => [
            'name' => 'addresses',
            'model' => \Larapress\Profiles\Models\PhysicalAddress::class,
            'provider' => \Larapress\Profiles\CRUD\PhysicalAddressCRUDProvider::class,
        ],
        'domains' => [
            'name' => 'domains',
            'model' => \Larapress\Profiles\Models\Domain::class,
            'provider' => \Larapress\Profiles\CRUD\DomainCRUDProvider::class,
        ],
        'devices' => [
            'name' => 'devices',
            'model' => \Larapress\Profiles\Models\Device::class,
            'provider' => \Larapress\Profiles\CRUD\DeviceCRUDProvider::class,
        ],
        'forms' => [
            'name' => 'forms',
            'model' => \Larapress\Profiles\Models\Form::class,
            'provider' => \Larapress\Profiles\CRUD\FormCRUDProvider::class,
        ],
        'form_entries' => [
            'name' => 'form-entries',
            'model' => \Larapress\Profiles\Models\FormEntry::class,
            'provider' => \Larapress\Profiles\CRUD\FormEntryCRUDProvider::class,
        ],
        'activity_logs' => [
            'name' => 'activity-logs',
            'model' => \Larapress\Profiles\Models\ActivityLog::class,
            'provider' => \Larapress\Profiles\CRUD\ActivityLogCRUDProvider::class,
        ],
        'segments' => [
            'name' => 'segments',
            'model' => \Larapress\Profiles\Models\Segment::class,
            'provider' => \Larapress\Profiles\CRUD\SegmentCRUDProvider::class,
        ],
        'groups' => [
            'name' => 'groups',
            'model' => \Larapress\Profiles\Models\Group::class,
            'provider' => \Larapress\Profiles\CRUD\GroupCRUDProvider::class,
        ],
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
        \Larapress\Profiles\CRUD\DeviceCRUDProvider::class,
        \Larapress\Profiles\CRUD\GroupCRUDProvider::class,
        \Larapress\Profiles\CRUD\PhysicalAddressCRUDProvider::class,
    ],
];
