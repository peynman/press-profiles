<?php

namespace Larapress\Profiles\Services;

use Illuminate\Support\Facades\Log;
use Larapress\CRUD\Services\IReportSource;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\Profiles\Services\FormEntryUpdateEvent;
use Larapress\Reports\Services\BaseReportSource;
use Larapress\Reports\Services\IReportsService;

class FormEntryUpdateReport implements IReportSource
{
    use BaseReportSource;

    /** @var IReportsService */
    private $reports;

    /** @var array */
    private $avReports;

    public function __construct(IReportsService $reports)
    {
        $this->reports = $reports;
        $this->avReports = [
            'form-entries.updated.total' => function ($user, array $options = []) {
                [$filters, $fromC, $toC] = $this->getCommonReportProps($user, $options);
                return $this->reports->queryMeasurement(
                    'form-entry.updated',
                    $filters,
                    ["domain"],
                    ["domain", "_value"],
                    $fromC,
                    $toC,
                    'count()'
                );
            },
            'form-entries.updated.windowed' => function ($user, array $options = []) {
                [$filters, $fromC, $toC] = $this->getCommonReportProps($user, $options);
                $window = isset($options['window']) ? $options['window'] : '1h';
                return $this->reports->queryMeasurement(
                    'form-entry.updated',
                    $filters,
                    ["domain"],
                    ["domain", "_value", "_time"],
                    $fromC,
                    $toC,
                    'aggregateWindow(every: '.$window.', fn: sum)'
                );
            }
        ];
    }

    public function handle(FormEntryUpdateEvent $event)
    {
        /** @var IRoleRepository */
        $roleRepo = app(IRoleRepository::class);
        $highRole = is_null($event->user) ? null : $roleRepo->getUserHighestRole($event->user);
        $tags = [
            'domain' => $event->domain->id,
            'role' => is_null($highRole) ? 'guest' : $highRole->name,
            'form' => $event->form->id,
            'created' => $event->created,
        ];

        if (isset($event->form->data['report_tags'])) {
            $values = $event->entry->data['values'];
            $askedTags = explode(',', $event->form->data['report_tags']);
            foreach($askedTags as $tag) {
                $val = Helpers::getArrayWithPath($values, $tag);
                $tags[$tag] = $val;
            }
        }

        if (!is_null($event->user)) {
            if ($event->form->id === config('larapress.profiles.defaults.profile-form-id')) {
                $tags['profile'] = true;
            }
        }

        $this->reports->pushMeasurement('form-entry.updated', 1, $tags, [], $event->timestamp);
    }
}
