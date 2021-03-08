<?php

namespace Larapress\Profiles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Larapress\Profiles\Services\Settings\ISettingsService;

class AppSettingOverride
{
    public function handle(Request $request, Closure $next)
    {
        /** @var IDomainRepository $domainRepo */
        $domainRepo = app(IDomainRepository::class);
        $domain = $domainRepo->getRequestDomain($request);

        /** @var ISettingsService $service  */
        $service = app(ISettingsService::class);
        $service->applyGlobalSettingsForDomain($domain);

        /** @var IProfileUser $user */
        $user = Auth::user();
        if (!is_null($user)) {
            $service->applyUserSettings($user);
        }

        return $next($request);
    }
}
