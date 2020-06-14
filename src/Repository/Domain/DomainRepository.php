<?php

namespace Larapress\Profiles\Repository\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Larapress\CRUD\BaseFlags;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\CRUD\DomainCRUDProvider;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

class DomainRepository implements IDomainRepository
{
    /**
     * @param IProfileUser|ICRUDUser $user
     * @param array $columns
     * @return Domain[]
     */
    public function getVisibleDomains($user, $columns = ['id', 'domain', 'data'])
    {
        $query = Domain::query();
        $provider = new DomainCRUDProvider();
        $query = $provider->onBeforeQuery($query);
        /** @var Domain[] $domains */
        $domains = $query->select($columns)->get();

        return $domains;
    }

    /**
     * @param Request|null $request
     *
     * @return Domain|null
     */
    public function getRequestDomain(Request $request)
    {
        $domain_str = $request->getHost();

        $domain = Domain::where(function(Builder $q) use ($domain_str) {
            $q->orWhere('domain', $domain_str);
            $q->orWhereHas('sub_domains', function(Builder $q) use($domain_str) {
                $q->where('sub_domain', $domain_str);
            });
        })->first();

        return $domain;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isRequestDefaultDomain(Request $request)
    {
        $domain = $this->getRequestDomain($request);
        return is_null($domain) || BaseFlags::isActive($domain->flags, UserDomainFlags::DEFAULT_DOMAIN);
    }

    /**
     * @return bool
     */
    public function isCurrentRequestDefaultDomain()
    {
        return $this->isRequestDefaultDomain(Request::createFromGlobals());
    }

    /**
     * @return Domain|null
     */
    public function getCurrentRequestDomain()
    {
        return $this->getRequestDomain(Request::createFromGlobals());
    }
}
