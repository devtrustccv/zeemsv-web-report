<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Submiss&atilde;o de Pedido</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #163344;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .page {
            position: relative;
            display: block;
            width: 210mm;
            min-height: 297mm;
            overflow: hidden;
            background: #ffffff;
        }

        .hero {
            position: relative;
            display: block;
            width: 210mm;
            height: 35.63mm;
        }

        .hero-image {
            display: block;
            width: 210mm;
            height: 35.63mm;
        }

        .brand {
            position: absolute;
            top: 12mm;
            left: 15mm;
            color: #ffffff;
        }

        .brand-logo {
            width: 44mm;
            max-height: 16mm;
        }

        .brand-name {
            color: #ffffff;
            font-size: 21px;
            font-weight: 800;
            line-height: 1;
        }

        .content {
            display: block;
            margin-top: -6.5mm;
            padding: 0 34px 96px;
        }

        .institution {
            margin-top: 2.1mm;
            margin-left: auto;
            width: 430px;
            text-align: right;
            color: #163344;
            line-height: 1.45;
            font-size: 10px;
        }

        .institution strong {
            display: block;
            margin-bottom: 4px;
            color: #005d91;
            font-weight: 800;
            font-size: 11px;
            white-space: nowrap;
        }

        .receipt-main {
            width: 100%;
            margin-top: 60px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .receipt-details {
            width: 76%;
            padding: 0;
            text-align: left;
            vertical-align: top;
        }

        .receipt-qr {
            width: 24%;
            padding: 0;
            text-align: right;
            vertical-align: top;
        }

        h1 {
            margin: 0 0 22px;
            color: #005d91;
            font-size: 12px;
            font-weight: 800;
        }

        .meta {
            color: #163344;
            line-height: 1.58;
            font-size: 11px;
        }

        .meta strong {
            color: #004b78;
            font-weight: 700;
        }

        .doc-summary {
            display: inline-block;
            width: 122px;
            text-align: center;
            color: #163344;
        }

        .doc-label {
            color: #17b7df;
            font-size: 23px;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
        }

        .doc-count {
            margin-left: 5px;
        }

        .qr-code,
        .qr-placeholder {
            display: block;
            width: 96px;
            height: 96px;
            margin: 13px auto 0;
        }

        .qr-placeholder {
            border: 1px solid #d6dde1;
            background: #f6f6f6;
        }

        .table-section {
            margin-top: 86px;
        }

        .table-section + .table-section {
            margin-top: 22px;
        }

        .section-title {
            margin: 0 0 10px;
            color: #163344;
            font-size: 11px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            height: 31px;
            padding: 0 12px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }

        .data-table th {
            background: #f1f1f1;
            color: #163344;
            font-weight: 700;
        }

        .data-table td {
            background: #f6f6f6;
            color: #163344;
        }

        .check {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid #607783;
            color: #004b78;
            font-size: 10px;
            line-height: 13px;
            text-align: center;
        }

        .validation {
            position: absolute;
            right: 22px;
            bottom: 0;
            left: 22px;
            text-align: center;
            color: #163344;
            font-size: 10px;
        }

        .portal-status {
            margin-bottom: 9px;
            font-weight: 700;
        }

        .validation-label {
            margin-bottom: 6px;
            font-weight: 700;
        }

        .validation-code {
            min-height: 54px;
            padding: 8px 20px;
            background: #e6f5fb;
            line-height: 1.5;
            word-break: break-word;
        }

        .error-state {
            width: 680px;
            margin: 210px auto 0;
            padding: 0 30px;
            text-align: center;
            color: #163344;
        }

        .error-state h1 {
            margin-bottom: 16px;
            font-size: 22px;
        }

        .error-state p {
            margin: 0;
            color: #496574;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<main class="page">
    @if ($error)
        <section class="error-state">
            <h1>N&atilde;o foi poss&iacute;vel carregar o recibo</h1>
            <p>{{ $error }}</p>
        </section>
    @else
        @php
            $headerImagePath = public_path('img/recibo-header.png');
            $headerImage = file_exists($headerImagePath)
                ? 'data:image/png;base64,'.base64_encode(file_get_contents($headerImagePath))
                : null;
        @endphp

        <div class="hero">
            @if ($headerImage)
                <img class="hero-image" src="{{ $headerImage }}" alt="">
            @endif
            <div class="brand" aria-label="ZEEMSV">
                @if (($renderingPdf ?? false) && ! empty($receipt['institution']['logo_pdf']))
                    <img class="brand-logo" src="{{ $receipt['institution']['logo_pdf'] }}" alt="ZEEMSV">
                @elseif (! ($renderingPdf ?? false) && ! empty($receipt['institution']['logo']))
                    <img class="brand-logo" src="{{ $receipt['institution']['logo'] }}" alt="ZEEMSV">
                @else
                    <div class="brand-name">ZEEMSV</div>
                @endif
            </div>
        </div>

        <div class="content">
            <div class="institution">
                <strong>{{ $receipt['institution']['name'] }}</strong><br>
                Contribuinte N.&ordm;: {{ $receipt['institution']['nif'] }}<br>
                {{ $receipt['institution']['address'] }}<br>
                Email: {{ $receipt['institution']['email'] }}<br>
                Telef. {{ $receipt['institution']['phone'] }}
            </div>

            <table class="receipt-main">
                <tr>
                    <td class="receipt-details">
                        <h1>Recibo de Submiss&atilde;o de Pedido</h1>

                        <div class="meta">
                            <strong>Tipo de Processo :</strong> {{ $receipt['process_type'] }}<br>
                            <strong>Processo n&ordm; :</strong> {{ $receipt['process_number'] }}<br>
                            <strong>Tipo de Solicita&ccedil;&atilde;o :</strong> {{ $receipt['request_type'] }}<br>
                            <strong>Entidade :</strong> {{ $receipt['entity'] }}<br>
                            <strong>Data de Entrada :</strong> {{ $receipt['entry_date'] }}<br>
                            <strong>Requerente :</strong> {{ $receipt['applicant'] }}<br>
                            <strong>NIF :</strong> {{ $receipt['nif'] }}
                        </div>
                    </td>
                    <td class="receipt-qr">
                        <div class="doc-summary">
                            <div class="doc-label">N&ordm; DOC <span class="doc-count">{{ $receipt['document_count'] }}</span></div>
                            @if (! empty($receipt['qr_code']))
                                <img class="qr-code" src="{{ $receipt['qr_code'] }}" alt="QR Code">
                            @else
                                <div class="qr-placeholder" aria-label="QR Code"></div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>

            <div class="table-section">
                <p class="section-title">Foram entregues os seguintes documentos :</p>

                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Documentos</th>
                        <th>Obrigat&oacute;rios</th>
                        <th>Sim</th>
                        <th>N&atilde;o</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($receipt['documents'] as $document)
                        <tr>
                            <td>{{ $document['name'] }}</td>
                            <td>{!! $document['required'] ? 'Sim' : 'N&atilde;o' !!}</td>
                            <td><span class="check">{!! $document['delivered'] ? '&#10003;' : '' !!}</span></td>
                            <td><span class="check">{!! ! $document['delivered'] ? '&#10003;' : '' !!}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Sem documentos associados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-section">
                <p class="section-title">Foram solicitados os seguintes requisitos :</p>

                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Requisito</th>
                        <th>Deve Cumprir?</th>
                        <th>Cumpre?</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($receipt['requirements'] as $requirement)
                        <tr>
                            <td>{{ $requirement['name'] }}</td>
                            <td>{!! $requirement['required'] ? 'Sim' : 'N&atilde;o' !!}</td>
                            <td>{!! $requirement['fulfilled'] ? 'Sim' : 'N&atilde;o' !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Sem requisitos associados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="validation">
            <div class="portal-status">
                Acompanhe o Estado do seu pedido no portal {{ $receipt['portal_url'] }}
            </div>
            <div class="validation-label">Contra Prova/Validation Code:</div>
            <div class="validation-code">{{ $receipt['validation_code'] ?? '---' }}</div>
        </footer>
    @endif
</main>
</body>
</html>
