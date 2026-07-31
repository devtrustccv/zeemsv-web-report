<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\BrowserPdfReportService;
use App\Services\PdfReportService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class FaturaProformaController extends Controller
{
    public function __construct(
        private readonly ApiService $apiService,
        private readonly BrowserPdfReportService $browserPdfReportService,
        private readonly PdfReportService $pdfReportService,
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function show(?int $idSolicitacao = null)
    {
        if ($idSolicitacao === null) {
            $invoice = $this->mockInvoice();

            return $this->pdfReportService->render('fatura-proforma', [
                'error' => null,
                'invoice' => $invoice,
                'headerImage' => $this->headerImageDataUri(),
            ], $this->filename($invoice));
        }

        try {
            $data = $this->apiService->fetchFaturaProformaDados($idSolicitacao);
        } catch (Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            return response()->view('fatura-proforma', [
                'error' => $exception->getMessage() ?: 'Ocorreu um problema ao carregar os dados da fatura proforma.',
                'invoice' => null,
                'headerImage' => $this->headerImageDataUri(),
            ], $status);
        }

        $invoice = $this->normalizeInvoice($data);
        $html = view('fatura-proforma', [
            'error' => null,
            'invoice' => $invoice,
            'headerImage' => $this->headerImageDataUri(),
        ])->render();
        $htmlUrl = rtrim(config('services.report.internal_url'), '/')
            . "/fatura-proforma/{$idSolicitacao}/html";

        try {
            return $this->browserPdfReportService->renderHtml(
                $html,
                $htmlUrl,
                $this->filename($invoice),
            );
        } catch (Throwable $browserException) {
            report($browserException);
        }

        return $this->pdfReportService->render('fatura-proforma', [
            'error' => null,
            'invoice' => $invoice,
            'headerImage' => $this->headerImageDataUri(),
        ], $this->filename($invoice));
    }

    public function showHtml(int $idSolicitacao)
    {
        try {
            $data = $this->apiService->fetchFaturaProformaDados($idSolicitacao);
        } catch (Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            return response()->view('fatura-proforma', [
                'error' => $exception->getMessage() ?: 'Ocorreu um problema ao carregar os dados da fatura proforma.',
                'invoice' => null,
                'headerImage' => null,
            ], $status);
        }

        return view('fatura-proforma', [
            'error' => null,
            'invoice' => $this->normalizeInvoice($data),
            'headerImage' => null,
        ]);
    }

    private function normalizeInvoice(array $data): array
    {
        $institution = $data['instituicao'] ?? $data['institution'] ?? [];
        $client = $data['cliente'] ?? $data['client'] ?? [];
        $number = $data['numero'] ?? $data['nr_fatura'] ?? $data['numero_fatura'] ?? null;
        $validationCode = $data['contra_prova']
            ?? $data['validation_code']
            ?? $data['codigo_validacao']
            ?? $data['codigo_contra_prova']
            ?? null;
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
            ?? $validationCode;
        $qrCodeImage = $this->looksLikeImageSource($rawQrCode) ? $rawQrCode : null;

        return [
            'institution' => [
                'name' => $institution['nome'] ?? $institution['name'] ?? 'ZEEMSV - ZONA ECONOMICA ESPECIAL MARITIMA EM SAO VICENTE',
                'nif' => $institution['nif'] ?? '---',
                'address' => $institution['endereco'] ?? $institution['address'] ?? '---',
                'email' => $institution['email'] ?? '---',
                'phone' => $institution['telefone'] ?? $institution['telemovel'] ?? $institution['phone'] ?? '---',
            ],
            'client' => [
                'salutation' => $client['saudacao'] ?? $client['salutation'] ?? 'Exmo.(a) Sr.(a)',
                'name' => $client['nome'] ?? $client['name'] ?? $data['cliente_nome'] ?? '---',
                'city' => $client['cidade'] ?? $client['city'] ?? '---',
                'country' => $client['pais'] ?? $client['country'] ?? $client['morada'] ?? '---',
            ],
            'title' => $data['titulo'] ?? $data['title'] ?? 'Fatura Pro Forma'.($number ? ' '.$number : ''),
            'type' => $data['tipo'] ?? $data['type'] ?? 'Original',
            'meta' => [
                'nif' => $client['nif'] ?? $data['nif'] ?? $data['cliente_nif'] ?? '---',
                'requisition' => $data['requisicao'] ?? $data['requisition'] ?? '',
                'currency' => $data['moeda'] ?? $data['currency'] ?? 'CVE',
                'exchange' => $this->formatNumber($data['cambio'] ?? $data['exchange'] ?? '1,00'),
                'date' => $this->formatDate($data['data'] ?? $data['date'] ?? null),
            ],
            'payment' => [
                'commercial_discount' => $this->formatNumber($data['desconto_comercial'] ?? data_get($data, 'payment.commercial_discount') ?? '0,00'),
                'additional_discount' => $this->formatNumber($data['desconto_adicional'] ?? data_get($data, 'payment.additional_discount') ?? '0,00'),
                'due_date' => $this->formatDate($data['vencimento'] ?? data_get($data, 'payment.due_date') ?? null),
                'condition' => $data['condicao_pagamento'] ?? data_get($data, 'payment.condition') ?? '---',
            ],
            'items' => $this->normalizeItems($data['items'] ?? $data['linhas'] ?? $data['artigos'] ?? []),
            'taxes' => $this->normalizeTaxes($data['taxes'] ?? $data['impostos'] ?? []),
            'validation_code' => $validationCode ?? '---',
            'qr_code' => $this->qrCodeService->toSvgDataUri($qrCodeContent) ?? $qrCodeImage,
            'summary' => $this->normalizeSummary($data),
            'loading' => $this->normalizeLoading($data['loading'] ?? $data['carga_descarga'] ?? $data),
            'bank_details' => $data['bank_details'] ?? $data['dados_bancarios'] ?? [
                'Dados Bancarios',
                'CECV 0002 0000 4308 8203 10105',
                'BCA 0003 0000 9245 1853 10176',
            ],
            'total' => $this->formatNumber($data['total'] ?? data_get($data, 'summary.total') ?? '0,00'),
        ];
    }

    private function normalizeItems(array $items): array
    {
        return array_map(fn (array $item) => [
            'article' => $item['artigo'] ?? $item['article'] ?? $item['codigo'] ?? '---',
            'description' => $item['descricao'] ?? $item['description'] ?? '---',
            'quantity' => $this->formatNumber($item['quantidade'] ?? $item['quantity'] ?? '0,00'),
            'unit' => $item['unidade'] ?? $item['unit'] ?? 'UN',
            'unit_price' => $this->formatNumber($item['preco_unitario'] ?? $item['unit_price'] ?? '0,00'),
            'discount' => $this->formatNumber($item['desconto'] ?? $item['discount'] ?? '0,00'),
            'tax' => $this->formatNumber($item['iva'] ?? $item['tax'] ?? '0,00'),
            'amount' => $this->formatNumber($item['valor'] ?? $item['amount'] ?? '0,00'),
        ], $items);
    }

    private function normalizeTaxes(array $taxes): array
    {
        return array_map(fn (array $tax) => [
            'rate' => $tax['taxa'] ?? $tax['rate'] ?? 'IVA (0,00)',
            'incidence' => $this->formatNumber($tax['incidencia'] ?? $tax['incidence'] ?? '0,00'),
            'total' => $this->formatNumber($tax['total'] ?? '0,00'),
        ], $taxes);
    }

    private function normalizeSummary(array $data): array
    {
        $summary = $data['summary'] ?? $data['resumo'] ?? [];

        return [
            'Mercadoria/Servicos' => $this->formatNumber($summary['mercadoria_servicos'] ?? $summary['Mercadoria/Servicos'] ?? $data['mercadoria_servicos'] ?? '0,00'),
            'Desconto Comercial' => $this->formatNumber($summary['desconto_comercial'] ?? $summary['Desconto Comercial'] ?? $data['desconto_comercial'] ?? '0,00'),
            'Desconto Adicional' => $this->formatNumber($summary['desconto_adicional'] ?? $summary['Desconto Adicional'] ?? $data['desconto_adicional'] ?? '0,00'),
            'Portes' => $this->formatNumber($summary['portes'] ?? $summary['Portes'] ?? '0,00'),
            'Outros Servicos' => $this->formatNumber($summary['outros_servicos'] ?? $summary['Outros Servicos'] ?? '0,00'),
            'Adiantamentos' => $this->formatNumber($summary['adiantamentos'] ?? $summary['Adiantamentos'] ?? '0,00'),
            'IEC/Outras Contribuicoes' => $this->formatNumber($summary['iec_outras_contribuicoes'] ?? $summary['IEC/Outras Contribuicoes'] ?? '0,00'),
            'Acertos' => $this->formatNumber($summary['acertos'] ?? $summary['Acertos'] ?? '0,00'),
        ];
    }

    private function normalizeLoading(array $data): array
    {
        return [
            'origin_title' => $data['origin_title'] ?? $data['titulo_carga'] ?? 'Carga',
            'origin_address' => $data['origin_address'] ?? $data['morada_carga'] ?? 'N/ Morada',
            'origin_city' => $data['origin_city'] ?? $data['cidade_carga'] ?? '---',
            'origin_code' => $data['origin_code'] ?? $data['codigo_carga'] ?? '',
            'destination_title' => $data['destination_title'] ?? $data['titulo_descarga'] ?? 'Descarga',
            'destination_address' => $data['destination_address'] ?? $data['morada_descarga'] ?? 'V/ Morada',
            'destination_city' => $data['destination_city'] ?? $data['cidade_descarga'] ?? '---',
            'destination_country' => $data['destination_country'] ?? $data['pais_descarga'] ?? '---',
        ];
    }

    private function formatDate(?string $date): string
    {
        if (! $date) {
            return '---';
        }

        try {
            return Carbon::parse($date)->format('d-m-Y');
        } catch (Throwable) {
            return $date;
        }
    }

    private function formatNumber(mixed $value): string
    {
        if (! is_numeric($value)) {
            return (string) $value;
        }

        return number_format((float) $value, 2, ',', ' ');
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

    private function filename(array $invoice): string
    {
        return str_replace('/', '-', strtolower($invoice['title'])).'.pdf';
    }

    private function headerImageDataUri(): ?string
    {
        $path = public_path('img/fatura-header.svg');

        if (! is_file($path)) {
            return null;
        }

        $bytes = file_get_contents($path);

        return $bytes ? 'data:image/svg+xml;base64,'.base64_encode($bytes) : null;
    }

    private function mockInvoice(): array
    {
        return [
            'institution' => [
                'name' => 'ZEEMSV - ZONA ECONOMICA ESPECIAL MARITIMA EM SAO VICENTE',
                'nif' => '300008210',
                'address' => 'CHA DE CRICKET - MINDELO - SAO VICENTE',
                'email' => 'info@zeemsv.cv',
                'phone' => '238 2315757',
            ],
            'client' => [
                'salutation' => 'Exmo.(a) Sr.(a)',
                'name' => 'TRUSS E EFFECTS - IMPORTACAO E EXPORTACAO, LDA',
                'city' => 'MINDELO',
                'country' => 'SAO VICENTE - CABO VERDE',
            ],
            'title' => 'Fatura Pro Forma FA 2026/8',
            'type' => 'Original',
            'meta' => [
                'nif' => '289290029',
                'requisition' => '',
                'currency' => 'CVE',
                'exchange' => '1,00',
                'date' => '2026-01-05',
            ],
            'payment' => [
                'commercial_discount' => '0,00',
                'additional_discount' => '0,00',
                'due_date' => '2026-01-05',
                'condition' => 'Pronto Pagamento',
            ],
            'items' => [
                [
                    'article' => '1001',
                    'description' => 'Auto - Cota de Adesao Lot. N. 02',
                    'quantity' => '1,00',
                    'unit' => 'UN',
                    'unit_price' => '26 785,00',
                    'discount' => '0,00',
                    'tax' => '0,00',
                    'amount' => '26 785,00',
                ],
            ],
            'taxes' => [
                [
                    'rate' => 'IVA (0,00)',
                    'incidence' => '26 785,00',
                    'total' => '0,00',
                ],
            ],
            'validation_code' => 'CV12601053090082100014010000000805939710546',
            'qr_code' => $this->qrCodeService->toSvgDataUri('CV12601053090082100014010000000805939710546'),
            'summary' => [
                'Mercadoria/Servicos' => '26 785,00',
                'Desconto Comercial' => '0,00',
                'Desconto Adicional' => '0,00',
                'Portes' => '0,00',
                'Outros Servicos' => '0,00',
                'Adiantamentos' => '0,00',
                'IEC/Outras Contribuicoes' => '0,00',
                'Acertos' => '0,00',
            ],
            'loading' => [
                'origin_title' => 'Carga',
                'origin_address' => 'N/ Morada - 2026-01-05 / 12:34',
                'origin_city' => 'CHA DE CRICKET - MINDELO - SAO VICENTE',
                'origin_code' => '1093',
                'destination_title' => 'Descarga',
                'destination_address' => 'V/ Morada',
                'destination_city' => 'MINDELO',
                'destination_country' => 'SAO VICENTE - CABO VERDE',
            ],
            'bank_details' => [
                'Dados Bancarios',
                'CECV 0002 0000 4308 8203 10105',
                'BCA 0003 0000 9245 1853 10176',
            ],
            'total' => '26 785,00',
        ];
    }
}
