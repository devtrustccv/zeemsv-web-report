<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ReciboPedidoController extends Controller
{
    public function __construct(private readonly ApiService $apiService) {}

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

        return view('recibo-pedido', [
            'error' => null,
            'receipt' => $this->normalizeReceipt($data),
        ]);
    }

    private function normalizeReceipt(array $data): array
    {
        $institution = $data['instituicao'] ?? [];

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
            'institution' => [
                'name' => $institution['nome'] ?? 'ZEEMSV - ZONA ECONOMICA ESPECIAL MARITIMA EM SAO VICENTE',
                'nif' => $institution['nif'] ?? '---',
                'email' => $institution['email'] ?? '---',
                'address' => $institution['endereco'] ?? '---',
                'phone' => $institution['telefone'] ?? $institution['telemovel'] ?? '---',
                'logo' => $institution['id_logo'] ?? null,
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
}
