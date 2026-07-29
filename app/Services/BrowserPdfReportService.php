<?php

namespace App\Services;

use Illuminate\Http\Response;
use Spatie\Browsershot\Browsershot;

class BrowserPdfReportService
{
    public function renderUrl(string $url, string $filename): Response
    {
        $filename = $this->sanitizeFilename($filename);

        if (function_exists('set_time_limit')) {
            set_time_limit(120);
        }

        $pdf = $this->makeBrowsershot($url)->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function renderHtml(string $html, string $contentUrl, string $filename): Response
    {
        $filename = $this->sanitizeFilename($filename);

        if (function_exists('set_time_limit')) {
            set_time_limit(120);
        }

        $pdf = $this->makeBrowsershotFromHtml($html, $contentUrl)->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    private function makeBrowsershot(string $url): Browsershot
    {
        return $this->configureBrowsershot(Browsershot::url($url));
    }

    private function makeBrowsershotFromHtml(string $html, string $contentUrl): Browsershot
    {
        return $this->configureBrowsershot(
            Browsershot::html($html)->setContentUrl($contentUrl),
        );
    }

    private function configureBrowsershot(Browsershot $browsershot): Browsershot
    {
        if (! is_dir(storage_path('app/browsershot'))) {
            mkdir(storage_path('app/browsershot'), 0775, true);
        }

        $browsershot
            ->setNodeModulePath(base_path('node_modules'))
            ->setCustomTempPath(storage_path('app/browsershot'))
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->setOption('waitUntil', 'load')
            ->timeout(90)
            ->windowSize(794, 1123);

        if ($chromePath = $this->chromePath()) {
            $browsershot->setChromePath($chromePath);
        }

        if ($this->shouldDisableSandbox()) {
            $browsershot->noSandbox();
        }

        return $browsershot;
    }

    private function chromePath(): ?string
    {
        $configuredPath = env('BROWSERSHOT_CHROME_PATH');

        if ($configuredPath) {
            return $configuredPath;
        }

        $candidates = PHP_OS_FAMILY === 'Windows'
            ? [
                'C:\Program Files\Google\Chrome\Application\chrome.exe',
                'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
                'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
                'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
            ]
            : [
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
                '/usr/bin/google-chrome',
                '/usr/bin/google-chrome-stable',
            ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function shouldDisableSandbox(): bool
    {
        return filter_var(env('BROWSERSHOT_NO_SANDBOX', PHP_OS_FAMILY !== 'Windows'), FILTER_VALIDATE_BOOL);
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $filename) ?: 'report.pdf';

        return str_ends_with(strtolower($filename), '.pdf') ? $filename : $filename.'.pdf';
    }
}
