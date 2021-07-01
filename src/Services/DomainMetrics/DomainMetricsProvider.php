<?php

namespace Larapress\Profiles\Services\DomainMetrics;

use Larapress\Reports\Services\Reports\IReportsServiceProvider;
use Larapress\Profiles\IProfileUser;

class DomainMetricsProvider implements IReportsServiceProvider
{
    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @param array $options
     * @return array
     */
    public function getFiltersForReports($user, $options)
    {
        $filters = [];

        if (!$user->hasRole(config('larapress.profiles.security.roles.super_role'))) {
            $filters['domain'] = $user->getAffiliateDomainIds();
        }

        return $filters;
    }
}
