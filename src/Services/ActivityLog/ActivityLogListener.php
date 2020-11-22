<?php

namespace Larapress\Profiles\Services\ActivityLog;

use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\CRUD\Events\CRUDCreated;
use Larapress\CRUD\Events\CRUDDeleted;
use Larapress\CRUD\Events\CRUDUpdated;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\Profiles\Models\ActivityLog;


class ActivityLogListener implements ShouldQueue
{
    public function handle($event)
    {
        $class = get_class($event);
        $user = $event->getUser();
        $provider = $event->getProvider();

        switch ($class) {
            case CRUDCreated::class:
                ActivityLog::create([
                    'user_id' => $event->userId,
                    'domain_id' => is_null($user) ? null : $user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_CREATE,
                    'subject' => '#'.$event->modelId,
                    'description' => 'ثبت '.class_basename($provider->getModelClass()).' جدید',
                    'data' => [
                        'provider' => get_class($provider),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->data['model'],
                    ]
                ]);
            break;
            case CRUDUpdated::class:
                ActivityLog::create([
                    'user_id' => $event->userId,
                    'domain_id' => is_null($user) ? null : $user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_EDIT,
                    'subject' => '#'.$event->modelId,
                    'description' => 'بروز رسانی '.class_basename($provider->getModelClass()).' با شناسه '.$event->modelId,
                    'data' => [
                        'provider' => get_class($provider),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->data['model'],
                    ]
                ]);
            break;
            case CRUDDeleted::class:
                ActivityLog::create([
                    'user_id' => $event->userId,
                    'domain_id' => is_null($user) ? null : $user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_DELETE,
                    'subject' => '#'.$event->modelId,
                    'description' => 'حذف '.class_basename($provider->getModelClass()).' با شناسه '.$event->modelId,
                    'data' => [
                        'provider' => get_class($provider),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->data['model'],
                    ]
                ]);
            break;
        }
    }
}
