<?php

namespace Larapress\Profiles\MetaData;

use Larapress\CRUDRender\Form\FormField;
use Larapress\Profiles\Models\Domain;

class UserAffiliateMetaData extends UserMetaData
{
    public function groupName()
    {
        return config('larapress.profiles.routes.user-affiliates.name');
    }

    public function getPermissionsVerbs()
    {
        return [
            self::VIEW,
            self::EDIT,
            self::DELETE,
            self::CREATE,
        ];
    }

    public function getMenuTitle()
    {
        return trans('models.affiliate.sidebar');
    }

    public function singular()
    {
        return trans('models.affiliate.name.singular');
    }

    public function plural()
    {
        return trans('models.affiliate.name.plural');
    }

    public function getMenuKey()
    {
        return config('larapress.profiles.routes.user-affiliates.name');
    }

    public function getControllerRouteName()
    {
        return config('larapress.profiles.routes.user-affiliates.name');
    }

    public function getViewControllerRouteName()
    {
        return config('larapress.profiles.routes.user-affiliates.name');
    }

    public function actions()
    {
        return [];
    }

    public function getCreateFields()
    {
        $rules = parent::getCreateFields();

        unset($rules[3]);
        unset($rules[4]);
        unset($rules[5]);

        return array_merge($rules, [
            FormField::text(
                'share_percent',
                trans('forms.label.share_percent'),
                null,
                null,
                'text',
                false,
                null,
                null,
                [
                    'mask' => 'percentage',
                    'mask-options' => [
                        'rightAlign' => true,
                    ],
                    'unmask-on-submit' => true,
                ]
            ),
            FormField::objectGroup(
                'affiliate_domains',
                trans('forms.label.affiliate_domains'),
                Domain::all(['id', 'domain']),
                FormField::decorator('id', 'domain', null, null, 'names'),
                []
            ),
        ]);
    }

    public function queryParams()
    {
        return [
            'with' => [
                'roles' => [],
            ],
        ];
    }

    public function getUpdateFields($object = null)
    {
        $rules = parent::getUpdateFields($object);

        unset($rules[3]);
        unset($rules[4]);
        unset($rules[5]);

        return array_merge($rules, [
            FormField::text(
                'share_percent',
                trans('forms.label.share_percent'),
                null,
                null,
                'text',
                false,
                null,
                null,
                [
                    'mask' => 'percentage',
                    'mask-options' => [
                        'rightAlign' => true,
                    ],
                    'unmask-on-submit' => true,
                ]
            ),
            FormField::objectGroup(
                'affiliate_domains',
                trans('forms.label.affiliate_domains'),
                Domain::all(['id', 'domain']),
                FormField::decorator('id', 'domain', null, null, 'names'),
                []
            ),
        ]);
    }

    public function getReportPages()
    {
    }
}
