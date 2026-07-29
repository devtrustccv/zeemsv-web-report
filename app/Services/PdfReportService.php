<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class PdfReportService
{
    public function render(string $view, array $data, string $filename): Response
    {
        $filename = $this->sanitizeFilename($filename);
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', base_path());
        $options->set('tempDir', storage_path('app/dompdf'));

        if (! is_dir(storage_path('app/dompdf'))) {
            mkdir(storage_path('app/dompdf'), 0775, true);
        }

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view($view, array_merge($data, [
            'renderingPdf' => true,
        ]))->render());
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $filename) ?: 'report.pdf';

        return str_ends_with(strtolower($filename), '.pdf') ? $filename : $filename.'.pdf';
    }
}
