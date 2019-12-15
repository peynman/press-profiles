<?php

namespace Larapress\Profiles\Base;

class MetricHelpers
{
    public static function getHoursDateRanges()
    {
        return [
            [
                'name' => '1_hours',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.hours', ['value' => 1]),
                'from' => ['now', ['addHours', -1]],
                'to' => ['now'],
                'resolution' => 'minutes',
            ],
            [
                'name' => '6_hours',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.hours', ['value' => 6]),
                'from' => ['now', ['addHours', -6]],
                'to' => ['now'],
                'resolution' => 'minutes',
            ],
            [
                'name' => '12_hours',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.hours', ['value' => 12]),
                'from' => ['now', ['addHours', -12]],
                'to' => ['now'],
                'resolution' => 'hours',
            ],
        ];
    }

    public static function getDaysDateRanges()
    {
        return [
            [
                'name' => 'today',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.today'),
                'from' => ['now', 'startOfDay'],
                'to' => ['now', 'endOfDay'],
                'resolution' => 'hours',
            ],
            [
                'name' => 'yesterday',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.yesterday'),
                'from' => ['now', ['addDays', -1], 'startOfDay'],
                'to' => ['now', ['addDays', -1], 'endOfDay'],
                'resolution' => 'hours',
            ],
            [
                'name' => '2_days',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.days', ['value' => 2]),
                'from' => ['now', ['addDays', -2]],
                'to' => ['now'],
                'resolution' => 'hours',
            ],
            [
                'name' => '5_days',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.days', ['value' => 5]),
                'from' => ['now', ['addDays', -5]],
                'to' => ['now'],
                'resolution' => 'days',
            ],
        ];
    }

    public static function getWeekMonthDateRanges()
    {
        return [
            [
                'name' => 'this_week',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.this_week'),
                'from' => ['now', 'startOfWeek'],
                'to' => ['now', 'endOfWeek'],
                'resolution' => 'days',
            ],
            [
                'name' => 'last_week',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.last_week'),
                'from' => ['now', ['addDays', -7], 'startOfWeek'],
                'to' => ['now', ['addDays', -7], 'endOfWeek'],
                'resolution' => 'days',
            ],
            [
                'name' => 'this_month',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.this_month'),
                'from' => ['now', 'startOfMonth'],
                'to' => ['now', 'endOfMonth'],
                'resolution' => 'days',
            ],
            [
                'name' => 'last_month',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.last_month'),
                'from' => ['now', ['addDays', -30], 'startOfMonth'],
                'to' => ['now', ['addDays', -30], 'endOfMonth'],
                'resolution' => 'days',
            ],
        ];
    }

    public static function getCustomDateRanges()
    {
        return [
            [
                'name' => 'custom',
                'label' => trans('dashboard.pages.metrics.dropdown.date_ranges.custom'),
                'from' => null,
                'to' => null,
                'resolution' => null,
            ],
        ];
    }

    public static function getCarbonFromDesc($desc)
    {
        $date = Carbon::now();
        foreach ($desc as $item) {
            if (is_string($item)) {
                switch ($item) {
                    case 'now':
                        $date = Carbon::now();
                        break;
                    case 'startOfDay':
                        $date->startOfDay();
                        break;
                    case 'endOfDay':
                        $date->endOfDay();
                        break;
                    case 'startOfWeek':
                        $date->startOfWeek();
                        break;
                    case 'endOfWeek':
                        $date->endOfWeek();
                        break;
                    case 'startOfMonth':
                        $date->startOfMonth();
                        break;
                    case 'endOfMonth':
                        $date->endOfMonth();
                        break;
                }
            } elseif (is_array($item)) {
                switch ($item['0']) {
                    case 'addDays':
                        $date->addDays($item[1]);
                        break;
                    case 'addHours':
                        $date->addHours($item[1]);
                        break;
                    case 'addMinutes':
                        $date->addMinutes($item[1]);
                        break;
                }
            }
        }

        return $date;
    }

    public static function getMetricLabelFunctionsForType($arr, $method, $maker, $idKey = 'id', $titleKey = 'title')
    {
        $labels = [];
        foreach ($arr as $a) {
            $labels[$a['title']] = [$method, $maker($a['id'])];
        }

        return $labels;
    }

    public static function getSubDomainLabelFunctions($method, $maker)
    {
        /** @var SubDomain[] $domains */
        $domains = SubDomain::select(['id', 'domain'])->get();
        $labels = [];
        foreach ($domains as $domain) {
            $labels[$domain->domain] = [$method, $maker($domain->domain)];
        }
        $labels['Main Site'] = [$method, $maker('default')];

        return $labels;
    }
}
