<?php

namespace Larapress\Profiles\Repository\Domain;

use Illuminate\Http\Request;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

interface IDomainRepository
{
    /**
     * @param IProfileUser|ICRUDUser $user
     * @param array $columns
     * @return Domain[]
     */
    public function getVisibleDomains(IProfileUser $user, $columns = ['id', 'domain', 'data']);

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return array
     */
    public function getDomainFlags(IProfileUser $user);

    /**
     * @param Request|null $request
     *
     * @return Domain|null
     */
    public function getRequestDomain(Request $request);

    /**
     * @param Request $request
     * @return bool
     */
    public function isRequestDefaultDomain(Request $request);

    /**
     * @return Domain|null
     */
    public function getCurrentRequestDomain();

    /**
     * @return bool
     */
    public function isCurrentRequestDefaultDomain();
}
