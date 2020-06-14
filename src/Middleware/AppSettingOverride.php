<?php

namespace Larapress\Profiles\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\Core\SessionService\ISessionService;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\Settings;
use Larapress\Profiles\Repository\Domain\IDomainRepository;

class AppSettingOverride
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \Larapress\Profiles\Repository\Domain\IDomainRepository $domainRepo */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getRequestDomain($request);

        $query = Settings::query()
            ->where('type', 'config')
            ->whereNull('user_id');
        if (!is_null($domain)) {
            $query->whereHas('domains', function(Builder $q) use($domain) {
                $q->where('id', $domain->id);
            });
        }

        $configs = $query->get();
        $overrides = [];
        foreach ($configs as $config) {
            $overrides[$config->key] = $config->val;
        }
        config($overrides);

        return $next($request);
    }
}
