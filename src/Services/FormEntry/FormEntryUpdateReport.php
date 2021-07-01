<?php

namespace Larapress\Profiles\Services\FormEntry;

use Carbon\Carbon;
use Larapress\CRUD\Services\CRUD\ICRUDReportSource;
use Larapress\Reports\Services\Reports\ReportSourceTrait;
use Larapress\Reports\Services\Reports\IReportsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Larapress\Reports\Services\Reports\IMetricsService;
use Larapress\Reports\Services\Reports\MetricsSourceProperties;

class FormEntryUpdateReport implements ICRUDReportSource, ShouldQueue
{
    use ReportSourceTrait;

    const MEASUREMENT_TYPE = 'form_entries';

    /** @var IReportsService */
    private $reports;
    /** @var IMetricsService */
    private $metrics;

    /** @var array */
    private $avReports;


    // start dot groups from 1 position_1.position_2.position_3...
    private $metricsDotGroups = [
        'form' => 2,
        'user' => 4,
        'domain' => 'domain_id',
    ];

    public function __construct()
    {
        $this->reports = app(IReportsService::class);
        $this->metrics = app(IMetricsService::class);

        $this->avReports = [
            'metrics.total.form_fill' => function ($user, array $options = []) {
                $props = MetricsSourceProperties::fromReportSourceOptions($user, $options, $this->metricsDotGroups);
                return $this->metrics->queryMeasurement(
                    'form_entires\.[0-9]*\.user\.[0-0]*$',
                    self::MEASUREMENT_TYPE,
                    null,
                    $props->filters,
                    $props->groups,
                    $props->domains,
                    $props->from,
                    $props->to,
                );
            },
            'metrics.windowed.form_fill' => function ($user, array $options = []) {
                $props = MetricsSourceProperties::fromReportSourceOptions($user, $options, $this->metricsDotGroups);
                return $this->metrics->aggregateMeasurement(
                    'form_entires\.[0-9]*\.user\.[0-0]*$',
                    self::MEASUREMENT_TYPE,
                    null,
                    $props->filters,
                    $props->groups,
                    $props->domains,
                    $props->from,
                    $props->to,
                    $props->window
                );
            }
        ];
    }

    /**
     * Undocumented function
     *
     * @param FormEntryUpdateEvent $event
     * @return void
     */
    public function handle(FormEntryUpdateEvent $event)
    {
        if (config('larapress.reports.metrics')) {
            $this->metrics->pushMeasurement(
                $event->domainId,
                self::MEASUREMENT_TYPE,
                'form:'.$event->formId,
                'form_entries.'.$event->formId.'.user.'.$event->userId,
                1,
                Carbon::now()
            );
        }
    }
}
