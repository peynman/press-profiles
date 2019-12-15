<?php


namespace Larapress\Profiles\Base;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\Builder;
use Larapress\CRUD\Base\ICRUDProvider;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class BaseCRUDQueryExporter
 * @package Larapress\CRUD\Base
 */
class CRUDExcelQueryExporter implements
    FromQuery,
    Responsable,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithHeadings
{
    use Exportable;
    /** @var \Illuminate\Database\Eloquent\Builder  */
    private $query;
    /**
     * @var ICRUDProvider
     */
    private $provider;

    /**
     * BaseCRUDQueryExporter constructor.
     *
     * @param Builder       $query
     * @param ICRUDProvider $provider
     */
    public function __construct(Builder $query, ICRUDProvider $provider)
    {
        $this->query = $query;
        $this->provider = $provider;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query->newQuery();
    }


    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        if (!is_null($this->provider)) {
            return $this->provider->getExportMap($row);
        }
        return $row;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        if (!is_null($this->provider)) {
            return $this->provider->getExportColumnTypes();
        }

        return [];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if (!is_null($this->provider)) {
            return $this->provider->getExportHeaders();
        }

        return [];
    }
}
