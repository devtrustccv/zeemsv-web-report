<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Submiss&atilde;o de Pedido</title>
    <style>
        :root {
            --blue: #005d91;
            --blue-strong: #004b78;
            --cyan: #17b7df;
            --ink: #163344;
            --muted: #496574;
            --soft: #f6f6f6;
            --soft-head: #f1f1f1;
            --footer: #e6f5fb;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            background: #4b4b4b;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            overflow: hidden;
            background: #fff;
        }

        .hero {
            position: relative;
            height: 156px;
            padding: 38px 40px 0;
            color: #fff;
            background: #063b70;
        }

        .hero-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        .brand {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            width: max-content;
        }

        .brand-logo {
            width: 126px;
            max-height: 44px;
            object-fit: contain;
            object-position: left center;
        }

        .brand-fallback {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-mark {
            position: relative;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background:
                radial-gradient(circle at 64% 28%, #7bdff0 0 17%, transparent 18%),
                conic-gradient(from 210deg, #0077ad, #32c3df, #64d1e2, #006b9f, #0077ad);
        }

        .brand-mark::after {
            content: "";
            position: absolute;
            inset: 7px 10px 8px 5px;
            background: #fff;
            border-radius: 60% 38% 55% 45%;
            transform: rotate(-32deg);
        }

        .brand-name {
            color: #fff;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            margin-top: -48px;
            padding: 0 34px 96px;
        }

        .institution {
            margin: 8px 0 0 auto;
            width: 430px;
            text-align: right;
            color: var(--ink);
            line-height: 1.45;
            font-size: 10px;
        }

        .institution strong {
            display: block;
            margin-bottom: 4px;
            color: var(--blue);
            font-weight: 800;
            font-size: 11px;
            white-space: nowrap;
        }

        .receipt-main {
            display: grid;
            grid-template-columns: 1fr 126px;
            gap: 42px;
            align-items: start;
            margin-top: 60px;
        }

        h1 {
            margin: 0 0 23px;
            color: var(--blue);
            font-size: 12px;
            font-weight: 800;
        }

        .meta {
            color: var(--ink);
            line-height: 1.58;
            font-size: 11px;
        }

        .meta strong {
            color: var(--blue-strong);
            font-weight: 700;
        }

        .doc-box {
            width: 126px;
            margin: 0 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--ink);
        }

        .doc-label {
            color: var(--cyan);
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
            background: var(--soft);
        }

        .table-section {
            margin-top: 98px;
        }

        .table-section + .table-section {
            margin-top: 24px;
        }

        .section-title {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            table-layout: fixed;
        }

        th,
        td {
            height: 34px;
            padding: 0 12px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }

        th {
            background: var(--soft-head);
            color: var(--ink);
            font-weight: 700;
        }

        td {
            background: var(--soft);
            color: var(--ink);
        }

        th:first-child,
        td:first-child {
            border-radius: 10px 0 0 10px;
        }

        th:last-child,
        td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .check {
            display: inline-grid;
            place-items: center;
            width: 15px;
            height: 15px;
            border: 1.2px solid #607783;
            border-radius: 5px;
            color: var(--blue-strong);
            font-size: 11px;
            line-height: 1;
        }

        .validation {
            position: absolute;
            right: 22px;
            bottom: 0;
            left: 22px;
            text-align: center;
            color: var(--ink);
            font-size: 10px;
        }

        .portal-status {
            margin-bottom: 9px;
            font-weight: 700;
        }

        .validation-label {
            margin-bottom: 11px;
            font-weight: 700;
        }

        .validation-code {
            min-height: 39px;
            padding: 8px 20px;
            background: var(--footer);
            line-height: 1.5;
            word-break: break-word;
        }

        .error-state {
            width: min(680px, 100%);
            margin: 210px auto 0;
            padding: 0 30px;
            text-align: center;
            color: var(--ink);
        }

        .error-state h1 {
            margin-bottom: 16px;
            font-size: 22px;
        }

        .error-state p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 210mm;
                min-height: 297mm;
                background: #fff;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
            }
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
        <header class="hero">
            <img class="hero-image" src="{{ $headerImage ?? '/img/recibo-header.png' }}" alt="">
            <div class="brand" aria-label="ZEEMSV">
                @if (! empty($receipt['institution']['logo_pdf']))
                    <img class="brand-logo" src="{{ $receipt['institution']['logo_pdf'] }}" alt="ZEEMSV">
                @elseif (! empty($receipt['institution']['logo']))
                    <img class="brand-logo" src="{{ $receipt['institution']['logo'] }}" alt="ZEEMSV">
                @else
                    <div class="brand-fallback">
                        <div class="brand-mark"></div>
                        <div class="brand-name">ZEEMSV</div>
                    </div>
                @endif
            </div>
        </header>

        <section class="content">
            <div class="institution">
                <strong>{{ $receipt['institution']['name'] }}</strong><br>
                Contribuinte N.&ordm;: {{ $receipt['institution']['nif'] }}<br>
                {{ $receipt['institution']['address'] }}<br>
                Email: {{ $receipt['institution']['email'] }}<br>
                Telef. {{ $receipt['institution']['phone'] }}
            </div>

            <section class="receipt-main">
                <div>
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
                </div>

                <aside class="doc-box">
                    <div class="doc-label">N&ordm; DOC <span class="doc-count">{{ $receipt['document_count'] }}</span></div>
                    @if (! empty($receipt['qr_code']))
                        <img class="qr-code" src="{{ $receipt['qr_code'] }}" alt="QR Code">
                    @else
                        <div class="qr-placeholder" aria-label="QR Code"></div>
                    @endif
                </aside>
            </section>

            <section class="table-section">
                <p class="section-title">Foram entregues os seguintes documentos :</p>

                <table>
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
            </section>

            <section class="table-section">
                <p class="section-title">Foram solicitados os seguintes requisitos :</p>

                <table>
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
            </section>
        </section>

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
