<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\BrowserPdfReportService;
use App\Services\PdfReportService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ReciboPedidoController extends Controller
{
    public function __construct(
        private readonly ApiService $apiService,
        private readonly BrowserPdfReportService $browserPdfReportService,
        private readonly PdfReportService $pdfReportService,
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function show(int $idSolicitacao)
    {
        try {
            $data = $this->apiService->fetchReciboPedidoDados($idSolicitacao);
        } catch (Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            return response()->view('recibo-pedido', [
                'error' => $exception->getMessage() ?: 'Ocorreu um problema ao carregar os dados do recibo.',
                'receipt' => null,
            ], $status);
        }

        $receipt = $this->normalizeReceipt($data);
        $filename = 'recibo-pedido-'.$receipt['process_number'].'.pdf';
        $html = view('recibo-pedido-html', [
            'error' => null,
            'receipt' => $receipt,
            'headerImage' => $this->headerImageDataUri(),
        ])->render();

        try {
            return $this->browserPdfReportService->renderHtml(
                $html,
                route('recibo-pedido.html', ['idSolicitacao' => $idSolicitacao]),
                $filename,
            );
        } catch (Throwable $browserException) {
            report($browserException);
        }

        return $this->pdfReportService->render('recibo-pedido', [
            'error' => null,
            'receipt' => $this->normalizeReceipt($data),
        ], $filename);
    }

    public function showHtml(int $idSolicitacao)
    {
        try {
            $data = $this->apiService->fetchReciboPedidoDados($idSolicitacao);
        } catch (Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            return response()->view('recibo-pedido-html', [
                'error' => $exception->getMessage() ?: 'Ocorreu um problema ao carregar os dados do recibo.',
                'receipt' => null,
            ], $status);
        }

        return view('recibo-pedido-html', [
            'error' => null,
            'receipt' => $this->normalizeReceipt($data),
            'headerImage' => null,
        ]);
    }

    private function normalizeReceipt(array $data, bool $preparePdfAssets = true): array
    {
        $institution = $data['instituicao'] ?? [];
        $rawQrCode = $data['qr_code']
            ?? $data['qrcode']
            ?? $data['qrCode']
            ?? $data['qr_url']
            ?? $data['url_qr']
            ?? null;
        $qrCodeContent = $data['qr_code_link']
            ?? $data['qrCodeLink']
            ?? $data['qrcode_link']
            ?? $data['link_qr']
            ?? $data['qr_link']
            ?? $data['url_validacao']
            ?? $data['validation_url']
            ?? ($this->looksLikeImageSource($rawQrCode) ? null : $rawQrCode)
            ?? null;
        $qrCodeImage = $this->looksLikeImageSource($rawQrCode) ? $rawQrCode : null;
        $logo = $institution['logo']
            ?? $institution['logotipo']
            ?? $institution['logo_url']
            ?? $institution['url_logo']
            ?? $institution['id_logo']
            ?? null;

        return [
            'document_count' => $data['nr_doc'] ?? count($data['documentos'] ?? []),
            'process_type' => $data['tipo_processo'] ?? '---',
            'process_number' => $data['nr_processo'] ?? '---',
            'request_type' => $data['tipo_solicitacao_descricao'] ?? $data['tipo_solicitacao'] ?? '---',
            'entity' => $data['entidade'] ?? '---',
            'entry_date' => $this->formatDate($data['data_entrada'] ?? null),
            'applicant' => $data['requerente'] ?? '---',
            'nif' => $data['nif'] ?? '---',
            'validation_code' => $data['contra_prova']
                ?? $data['validation_code']
                ?? $data['codigo_validacao']
                ?? $data['codigo_contra_prova']
                ?? $data['codigo']
                ?? null,
            'portal_url' => $institution['link_portal']
                ?? $institution['link_portaç']
                ?? $data['link_portal']
                ?? $data['portal_url']
                ?? $data['url_portal']
                ?? 'https://zeemsv-sta.unitelmais.cv/pt',
            'qr_code' => $this->qrCodeService->toSvgDataUri($qrCodeContent) ?? $qrCodeImage,
            'institution' => [
                'name' => $institution['nome'] ?? 'ZEEMSV - ZONA ECONOMICA ESPECIAL MARITIMA EM SAO VICENTE',
                'nif' => $institution['nif'] ?? '---',
                'email' => $institution['email'] ?? '---',
                'address' => $institution['endereco'] ?? '---',
                'phone' => $institution['telefone'] ?? $institution['telemovel'] ?? '---',
                'logo' => $logo,
                'logo_pdf' => $preparePdfAssets ? $this->pdfSafeImageDataUri($logo) : null,
            ],
            'documents' => array_map(fn (array $document) => [
                'name' => $document['documento'] ?? $document['codigo'] ?? '---',
                'required' => $this->isAffirmative($document['obrigatorio'] ?? false),
                'delivered' => $this->isAffirmative($document['entregue'] ?? $document['sim'] ?? false),
            ], $data['documentos'] ?? []),
            'requirements' => array_map(fn (array $requirement) => [
                'name' => $requirement['requisito'] ?? '---',
                'required' => $this->isAffirmative($requirement['obrigatorio'] ?? false),
                'fulfilled' => $this->isAffirmative($requirement['cumprido'] ?? $requirement['sim'] ?? false),
            ], $data['requisitos'] ?? []),
        ];
    }

    private function formatDate(?string $date): string
    {
        if (! $date) {
            return '---';
        }

        try {
            return Carbon::parse($date)->format('d-m-Y');
        } catch (\Throwable) {
            return $date;
        }
    }

    private function isAffirmative(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(strtolower(trim((string) $value)), ['sim', 's', 'yes', 'true', '1'], true);
    }

    private function looksLikeImageSource(mixed $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        if (str_starts_with($value, 'data:image/')) {
            return true;
        }

        return (bool) preg_match('/\.(svg|png|jpe?g|gif|webp)(\?.*)?$/i', $value);
    }

    private function pdfSafeImageDataUri(mixed $source): ?string
    {
        $source = trim((string) $source);

        if ($source === '') {
            return null;
        }

        [$bytes, $mime] = $this->readImageSource($source);

        if (! $bytes) {
            return null;
        }

        if ($mime === 'image/webp') {
            $pngBytes = $this->convertWebpToPng($bytes);

            return $pngBytes
                ? 'data:image/png;base64,'.base64_encode($pngBytes)
                : 'data:image/webp;base64,'.base64_encode($bytes);
        }

        if (in_array($mime, ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/svg+xml'], true)) {
            return 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        return null;
    }

    private function readImageSource(string $source): array
    {
        if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $source, $matches)) {
            return [base64_decode($matches[2], true) ?: null, strtolower($matches[1])];
        }

        if (filter_var($source, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(10)
                    ->accept('*/*')
                    ->get($source);
            } catch (Throwable) {
                return [null, null];
            }

            if (! $response->successful()) {
                return [null, null];
            }

            $bytes = $response->body();
            $mime = strtolower((string) $response->header('Content-Type'));
            $mime = trim(explode(';', $mime)[0]);

            return [$bytes, str_starts_with($mime, 'image/') ? $mime : $this->detectImageMime($bytes)];
        }

        $path = str_starts_with($source, public_path())
            ? $source
            : public_path(ltrim($source, '/\\'));

        if (! is_file($path)) {
            return $this->readRelativeRemoteImageSource($source);
        }

        $bytes = file_get_contents($path);

        return [$bytes ?: null, $this->detectImageMime($bytes ?: '')];
    }

    private function detectImageMime(string $bytes): ?string
    {
        return $bytes !== ''
            ? (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes) ?: null
            : null;
    }

    private function readRelativeRemoteImageSource(string $source): array
    {
        $source = ltrim($source, '/\\');
        $apiUrl = rtrim((string) env('LINK_API_ZEEMSV'), '/');

        $candidates = [];

        if ($apiUrl !== '') {
            $candidates[] = $apiUrl.'/'.$source;

            $parts = parse_url($apiUrl);

            if (! empty($parts['scheme']) && ! empty($parts['host'])) {
                $origin = $parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
                $candidates[] = $origin.'/'.$source;
            }
        }

        foreach (array_unique($candidates) as $candidate) {
            [$bytes, $mime] = $this->readImageSource($candidate);

            if ($bytes) {
                return [$bytes, $mime];
            }
        }

        return [null, null];
    }

    private function convertWebpToPng(string $bytes): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $image = @imagecreatefromstring($bytes);

        if (! $image) {
            return null;
        }

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return ob_get_clean() ?: null;
    }

    private function headerImageDataUri(): ?string
    {
        $path = public_path('img/recibo-header.png');

        if (! is_file($path)) {
            return null;
        }

        $bytes = file_get_contents($path);

        return $bytes ? 'data:image/png;base64,'.base64_encode($bytes) : null;
    }
}
