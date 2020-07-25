<?php

namespace Larapress\Profiles\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Larapress\CRUD\Services\ICRUDExporter;
use Larapress\CRUD\Services\ICRUDProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BaseCRUDQueryExport implements ICRUDExporter
{
    public function getResponseForQueryExport(Request $request, Builder $query, ICRUDProvider $provider)
    {
        return new StreamedResponse(function () use ($query, $provider) {
            $FH = fopen('php://output', 'w');
            $query->chunk(500, function ($items) use ($FH, $provider) {
                foreach ($items as $row) {
                    fputcsv($FH, $provider->getExportArray($row));
                }
            });
            fclose($FH);
        }, 200, [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=galleries.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ]);
    }
}
