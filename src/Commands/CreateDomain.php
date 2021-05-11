<?php

namespace Larapress\Profiles\Commands;

use Illuminate\Console\Command;
use Larapress\Profiles\Flags\UserDomainFlags;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\IProfileUser;

class CreateDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:profiles:create-domain {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Domain.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domainName = $this->option('domain');
        if (is_null($domainName)) {
            $domainName = $this->ask('Enter domain to add');
        }
        $domain = Domain::updateOrCreate([
            'domain' => $domainName,
            'author_id' => 1,
        ]);

        /** @var Builder $user_quer */
        $user_query = call_user_func([config('larapress.crud.user.class'), 'query']);
        /** @var IProfileUser $user */
        $users = $user_query->whereHas('roles', function ($q) {
            $q->where('id', 1);
        })->get();
        foreach ($users as $user) {
            $user->domains()->attach($domain, [
                'flags' => UserDomainFlags::REGISTRATION_DOMAIN | UserDomainFlags::MEMBERSHIP_DOMAIN,
            ]);
            $user->forgetDomainsCache();
        }
        $this->info('done.');
        return 0;
    }
}
