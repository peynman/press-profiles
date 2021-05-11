<?php

use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Flags\UserFlags;
use Larapress\Profiles\Models\PhoneNumber;

return [
    'users' => [
        'flags' => [
            UserFlags::VERIFICATION_INFO_SENT => 'اطلاعات کاربری ارسال شده',
            UserFlags::VERIFIED_USER => 'تایید شده',
            UserFlags::BANNED => 'قطع دسترسی'
        ]
    ],

    'domains' => [
        'flags' => [
            UserDomainFlags::MEMBERSHIP_DOMAIN => 'دامنه عضویت',
            UserDomainFlags::REGISTRATION_DOMAIN => 'دامنه ثبت نام',
            UserDomainFlags::AFFILIATE_DOMAIN => 'صاحب امتیاز',
            UserDomainFlags::DEFAULT_DOMAIN => 'پیش فرض',
        ]
    ],

    'phone-numbers' => [
        'flags' => [
            PhoneNumber::FLAGS_DO_NOT_CONTACT => 'تماس گرفته نشود',
            PhoneNumber::FLAGS_VERIFIED => 'تایید شده',
        ],
    ],
];
