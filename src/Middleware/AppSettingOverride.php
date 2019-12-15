<?php


namespace Larapress\Profiles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Models\Settings;
use Larapress\Profiles\Models\Domain;
use Larapress\Core\SessionService\ISessionService;

class AppSettingOverride
{
    public function handle(Request $request, Closure $next)
    {
        $domain = Domain::select(['id'])
                           ->where('domain', $request->getHost())->first();
        $domain_ids = null;
        if (!is_null($domain)) {
            /** @var ICRUDUser[] $master_aff */
            $master_aff = $domain->affiliates()->whereHas('roles', function ($q) {
                $q->where('name', config('crud.roles.master.role_name'));
            })->get();

            if (count($master_aff) == 1) {
                $domain_ids = $master_aff[0]->getAffiliateDomainIds();
            }
        }
        $query = Settings::query()->where('type', 'config')->whereNull('user_id');
        if (is_null($domain_ids)) {
            $query->where('sub_domain_id', $domain_ids);
        } else {
            $query->whereIn('sub_domain_id', $domain_ids);
        }

        $configs = $query->get();
        $overrides = [];
        foreach ($configs as $config) {
            $overrides[$config->key] = $config->val;
        }
        config($overrides);

        try {
            /** @var ISessionService $sessionService */
            $sessionService = app()->make(ISessionService::class);
            $sessionLocale = $sessionService->getForUser('locale', Auth::user(), app()->getLocale());
            app()->setLocale($sessionLocale);
        } catch (\Exception $e) {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
