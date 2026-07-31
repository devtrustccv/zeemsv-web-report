<?php
namespace App\Services;

use App\Helpers\AESHelper;
/* use App\Services\ApiToken; */
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ApiService
{
   /*  protected $apiToken;

    public function __construct(ApiToken $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public function getHttpHeaders($token)
    {

        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    } */
    protected function handleErrorResponse($response)
    {
        $status  = $response->status();
        $message = $response->json('message') ?? $response->json('error') ?? $response->body() ?? 'Erro ao acessar a API';

        abort($status, $message);
    }

    /**
     * Requisicao GET para buscar qualificacao completa pelo ID.
     */
    public function fetchQualificacaoCompleta($id)
    {
        try {
            $realId = AESHelper::decrypt($id);
            if (! is_numeric($realId)) {
                throw new \Exception('ID invalido apos descriptografar.');
            }
        } catch (\Exception $e) {
            $realId = $id;
        }

        $url   = env('LINK_API_CNQ') . "/qualificacao/{$realId}/completo";
        //$token = $this->apiToken->makeApiRequestToken();

        $response = Http::withOptions(['verify' => false])
         //->withHeaders($this->getHttpHeaders($token))
            ->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['fault'])) {
                abort(404, $responseData['fault']['detail']);
            }

            return $responseData;
        }

        $this->handleErrorResponse($response);
    }

    public function fetchCandidateSelect()
    {

        $url = env('LINK_API_SGF') . "/candidatura/selecionados_agrupado";
        // $token = $this->apiToken->makeApiRequestToken();

        $response = Http::withOptions(['verify' => false])
        //  ->withHeaders($this->getHttpHeaders($token))
            ->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['fault'])) {
                abort(404, $responseData['fault']['detail']);
            }

            return $responseData;
        }

        $this->handleErrorResponse($response);
    }

    public function fetchReciboPedidoDados($idSolicitacao)
    {
        $baseUrl = rtrim(env('LINK_API_ZEEMSV'), '/');
        if (! $baseUrl) {
            abort(500, 'Servico de recibo nao configurado.');
        }

        $url = "{$baseUrl}/solicitacaos/{$idSolicitacao}/recibo-dados";

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->accept('*/*')
                ->get($url);
        } catch (ConnectionException) {
            abort(503, 'Nao foi possivel comunicar com o servico de recibo. Tente novamente mais tarde.');
        }

        if ($response->successful()) {
            $responseData = $response->json();

            if (($responseData['success'] ?? false) !== true || ! isset($responseData['data'])) {
                abort(404, $responseData['message'] ?? 'Dados do recibo nao encontrados.');
            }

            return $responseData['data'];
        }

        $this->handleErrorResponse($response);
    }

    public function fetchFaturaProformaDados($idSolicitacao)
    {
        $baseUrl = rtrim(env('LINK_API_ZEEMSV'), '/');
        if (! $baseUrl) {
            abort(500, 'Servico de fatura proforma nao configurado.');
        }

        $url = "{$baseUrl}/solicitacaos/{$idSolicitacao}/fatura-proforma-dados";

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->accept('*/*')
                ->get($url);
        } catch (ConnectionException) {
            abort(503, 'Nao foi possivel comunicar com o servico de fatura proforma. Tente novamente mais tarde.');
        }

        if ($response->successful()) {
            $responseData = $response->json();

            if (($responseData['success'] ?? false) !== true || ! isset($responseData['data'])) {
                abort(404, $responseData['message'] ?? 'Dados da fatura proforma nao encontrados.');
            }

            return $responseData['data'];
        }

        $this->handleErrorResponse($response);
    }

    public function fetchRvcc($id)
    {

        try {
            $realId = AESHelper::decrypt($id);
            if (! is_numeric($realId)) {
                throw new \Exception('ID invalido apos descriptografar.');
            }
        } catch (\Exception $e) {
            $realId = $id;
        }

        $url = env('LINK_API_CNQ') . "/qualificacao/rvcc/{$realId}";
        //$token = $this->apiToken->makeApiRequestToken();

        $response = Http::withOptions(['verify' => false])
        //->withHeaders($this->getHttpHeaders($token))
            ->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['fault'])) {
                abort(502, $responseData['fault']['detail']);
            }

            return $responseData;
        }

        $this->handleErrorResponse($response);
    }

    /**
     * Requisicao GET para buscar RVCC de uma UC especifica pelo ID.
     */
    public function fetchRvccUc($id, $ucId)
    {
        try {
            $realId = AESHelper::decrypt($id);
            if (! is_numeric($realId)) {
                throw new \Exception('ID invalido apos descriptografar.');
            }
        } catch (\Exception $e) {
            $realId = $id;
        }

        try {
            $realUcId = AESHelper::decrypt($ucId);
            if (! is_numeric($realUcId)) {
                throw new \Exception('UC ID invalido apos descriptografar.');
            }
        } catch (\Exception $e) {
            $realUcId = $ucId;
        }

        $url = env('LINK_API_CNQ') . "/qualificacao/rvcc/{$realId}/uc/{$realUcId}";
        //$token = $this->apiToken->makeApiRequestToken();

        $response = Http::withOptions(['verify' => false])
            //->withHeaders($this->getHttpHeaders($token))
            ->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['fault'])) {
                abort(502, $responseData['fault']['detail']);
            }

            return $responseData;
        }

        $this->handleErrorResponse($response);
    }
}
