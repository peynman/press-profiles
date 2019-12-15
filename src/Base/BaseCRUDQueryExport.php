<?php

namespace Larapress\Profiles\Base;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Larapress\CRUD\Base\ICRUDExporter;
use Larapress\CRUD\Base\ICRUDProvider;
use Maatwebsite\Excel\Excel;

class BaseCRUDQueryExport implements ICRUDExporter
{
    public function getResponseForQueryExport(Request $request, Builder $query, ICRUDProvider $provider)
    {
        $download = (new CRUDExcelQueryExporter($query, $provider));
        if (!is_null($request->get('format'))) {
            switch ($request->get('format')) {
                case 'html':
                    return $download->download('export-'.Carbon::now()->timestamp.'.html', Excel::HTML);
                case 'pdf':
                    return $download->download('export-'.Carbon::now()->timestamp.'.pdf', Excel::MPDF);
            }
        }
        return $download->download('export-'.Carbon::now()->timestamp.'.xlsx');
    }
}
