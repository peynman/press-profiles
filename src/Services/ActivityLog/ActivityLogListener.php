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
        switch ($class) {
            case CRUDCreated::class:
                ActivityLog::create([
                    'user_id' => is_null($event->user) ? null :$event->user->id,
                    'domain_id' => $event->user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_CREATE,
                    'subject' => '#'.$event->model->id,
                    'description' => 'ثبت '.class_basename($event->getProvider()->getModelClass()).' جدید',
                    'data' => [
                        'provider' => get_class($event->getProvider()),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->model,
                    ]
                ]);
            break;
            case CRUDUpdated::class:
                ActivityLog::create([
                    'user_id' => is_null($event->user) ? null :$event->user->id,
                    'domain_id' => $event->user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_EDIT,
                    'subject' => '#'.$event->model->id,
                    'description' => 'بروز رسانی '.class_basename($event->getProvider()->getModelClass()).' با شناسه '.$event->model->id,
                    'data' => [
                        'provider' => get_class($event->getProvider()),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->model,
                    ]
                ]);
            break;
            case CRUDDeleted::class:
                ActivityLog::create([
                    'user_id' => is_null($event->user) ? null :$event->user->id,
                    'domain_id' => $event->user->getMembershipDomainId(),
                    'type' => ActivityLog::TYPE_CRUD_DELETE,
                    'subject' => '#'.$event->model->id,
                    'description' => 'حذف '.$event->getProvider()->getModelClass().' با شناسه '.$event->model->id,
                    'data' => [
                        'provider' => get_class($event->getProvider()),
                        'recorded_at' => $event->timestamp,
                        'model' => $event->model,
                    ]
                ]);
            break;
        }
    }
}
