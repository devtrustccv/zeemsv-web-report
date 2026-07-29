<?php

namespace App\Http\Controllers;

use App\Services\PdfReportService;

class FaturaProformaController extends Controller
{
    public function __construct(private readonly PdfReportService $pdfReportService) {}

    public function show()
    {
        $invoice = $this->mockInvoice();

        return $this->pdfReportService->render('fatura-proforma', [
            'invoice' => $invoice,
        ], str_replace('/', '-', strtolower($invoice['title'])).'.pdf');
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
                    'quantity' => '',
                    'total' => '0,00',
                ],
            ],
            'validation_code' => 'CV12601053090082100014010000000805939710546',
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
