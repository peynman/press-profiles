<?php

namespace Larapress\Profiles\Services\DomainMetrics;

use Larapress\Reports\Services\IReportsServiceProvider;

class DomainMetricsProvider implements IReportsServiceProvider
{
    /**
     * Undocumented function
     *
     * @param ICRUDUser $user
     * @param array $options
     * @return array
     */
    public function getFiltersForReports($user, $options)
    {
        $filters = [];

        if (!$user->hasRole(config('larapress.profiles.security.roles.super-role'))) {
            if ($user->hasRole(config('larapress.ecommerce.lms.support_role_id'))) {
                $filters['support'] = $user->id;
            } else {
                $filters['domain'] = $user->getAffiliateDomainIds();
            }
        }

        return $filters;
    }
}
