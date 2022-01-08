<?php

namespace Larapress\Profiles\Services\DeviceMonitor;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\Auth\Services\Signin\SigninEvent;
use Larapress\CRUD\Events\CRUDCreated;
use Larapress\Profiles\CRUD\DeviceCRUDProvider;
use Larapress\Profiles\Models\Device;

class SigninEventListener implements ShouldQueue
{
    public function handle(SigninEvent $event)
    {
        $user = $event->getUser();
        $device = Device::updateOrCreate([
            'user_id' => $event->userId,
            'domain_id' => $user->getMembershipDomainId(),
            'client_type' => $event->requestClientType,
            'client_agent' => $event->requestAgent,
        ], [
            'client_ip' => $event->requestIp,
        ]);

        CRUDCreated::dispatch($user, $device, DeviceCRUDProvider::class, Carbon::now());
    }
}
