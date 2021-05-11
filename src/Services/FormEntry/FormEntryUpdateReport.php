<?php

namespace Larapress\Profiles\Services\FormEntry;

use Illuminate\Support\Facades\Log;
use Larapress\CRUD\Services\CRUD\ICRUDReportSource;
use Larapress\CRUD\Extend\Helpers;
use Larapress\CRUD\Repository\IRoleRepository;
use Larapress\Reports\Services\BaseReportSource;
use Larapress\Reports\Services\IReportsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class FormEntryUpdateReport implements ICRUDReportSource, ShouldQueue
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
        $user = $event->getUser();
        $form = $event->getForm();

        $supportProfileId = is_null($user) ? null : $user->getSupportUserId();

        $highRole = is_null($user) ? null : $user->getUserHighestRole()->name;

        $tags = [
            'domain' => $event->domainId,
            'role' => is_null($highRole) ? 'guest' : $highRole,
            'form' => $event->formId,
            'created' => $event->created,
            'support' => $supportProfileId,
        ];

        if (isset($form->data['report_tags'])) {
            $entry = $event->getFormEntry();
            $values = $entry->data['values'];
            $askedTags = explode(',', $form->data['report_tags']);
            foreach ($askedTags as $tag) {
                $val = Helpers::getArrayWithPath($values, $tag);
                $tags[$tag] = $val;
            }
        }

        if (!is_null($user)) {
            if ($form->id === config('larapress.profiles.default_profile_form_id')) {
                $tags['profile'] = true;
            }
        }

        $this->reports->pushMeasurement('form-entry.updated', 1, $tags, [], $event->timestamp);
    }
}
