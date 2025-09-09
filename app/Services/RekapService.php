<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RekapExport;

class RekapService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function exportExcel($data, $filename = 'rekap.xlsx')
    {
        return Excel::download(new RekapExport($data), $filename);
    }

    public static function exportPdf($view, $data, $filename = 'rekap.pdf')
    {
        $pdf = Pdf::loadView($view, $data);
        return response()->streamDownload(fn () => print($pdf->output()), $filename);
    }
}
