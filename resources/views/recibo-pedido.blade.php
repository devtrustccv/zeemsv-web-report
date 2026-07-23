<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Submiss&atilde;o de Pedido</title>
    <style>
        :root {
            --blue: #006aa6;
            --dark-blue: #004d77;
            --muted: #6f7f89;
            --soft: #f1f3f4;
            --table-head: #e5e7e8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #4a4a4a;
            color: #18313f;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .page {
            width: 1120px;
            min-height: 790px;
            margin: 16px auto;
            background: #fff;
            border: 4px solid var(--blue);
            padding: 44px 28px 42px;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 40px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 172px;
            max-height: 58px;
            object-fit: contain;
            object-position: left center;
        }

        .brand-fallback {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background:
                radial-gradient(circle at 64% 28%, #36c7e6 0 16%, transparent 17%),
                conic-gradient(from 205deg, #0074ad, #1fb3d2, #50bdd4, #00598a, #0074ad);
            position: relative;
        }

        .brand-mark::after {
            content: "";
            position: absolute;
            inset: 10px 15px 12px 7px;
            background: #fff;
            border-radius: 60% 38% 55% 45%;
            transform: rotate(-32deg);
        }

        .brand-name {
            color: #23739c;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .entity-info {
            max-width: 560px;
            text-align: right;
            color: #1d4356;
            line-height: 1.8;
            font-size: 13px;
        }

        .entity-info strong {
            color: var(--dark-blue);
            font-weight: 800;
        }

        .receipt-grid {
            display: grid;
            grid-template-columns: 1fr 180px;
            gap: 46px;
            margin-top: 86px;
            align-items: start;
        }

        h1 {
            margin: 0 0 34px;
            color: var(--dark-blue);
            font-size: 15px;
            font-weight: 800;
        }

        .meta {
            line-height: 1.7;
            color: var(--muted);
        }

        .meta strong {
            color: #24465a;
            font-weight: 700;
        }

        .doc-box {
            width: 180px;
            min-height: 170px;
            border: 1.5px solid #50a3cf;
            border-radius: 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #0d3144;
            margin-top: 42px;
        }

        .doc-label {
            font-size: 29px;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1;
        }

        .doc-count {
            font-size: 58px;
            font-weight: 400;
            line-height: 1;
            margin-top: 6px;
        }

        .table-section {
            margin-top: 48px;
        }

        .table-section + .table-section {
            margin-top: 30px;
        }

        .section-title {
            margin: 0 0 18px;
            color: #18313f;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        th,
        td {
            height: 48px;
            padding: 0 24px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background: var(--table-head);
            color: #19394d;
            font-weight: 500;
        }

        td {
            background: var(--soft);
            color: #7a8a93;
        }

        th:first-child,
        td:first-child {
            border-radius: 14px 0 0 14px;
        }

        th:last-child,
        td:last-child {
            border-radius: 0 14px 14px 0;
        }

        .check {
            display: inline-grid;
            place-items: center;
            width: 18px;
            height: 18px;
            border: 1.4px solid #6f8793;
            border-radius: 5px;
            color: #1a526c;
            font-size: 13px;
            line-height: 1;
        }

        .error-state {
            width: min(680px, 100%);
            margin: 150px auto 0;
            text-align: center;
            color: #24465a;
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

        @media print {
            body {
                background: #fff;
            }

            .page {
                width: 297mm;
                min-height: 210mm;
                margin: 0;
                border-width: 3px;
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
    <header class="topbar">
        <div class="brand" aria-label="ZEEMSV">
            @if (! empty($receipt['institution']['logo']))
                <img class="brand-logo" src="{{ $receipt['institution']['logo'] }}" alt="ZEEMSV">
            @else
                <div class="brand-fallback">
                    <div class="brand-mark"></div>
                    <div class="brand-name">ZEEMSV</div>
                </div>
            @endif
        </div>

        <div class="entity-info">
            <strong>{{ $receipt['institution']['name'] }}</strong><br>
            Contribuinte N.&ordm;: {{ $receipt['institution']['nif'] }}<br>
            {{ $receipt['institution']['address'] }}<br>
            Email: {{ $receipt['institution']['email'] }}<br>
            Telef. {{ $receipt['institution']['phone'] }}
        </div>
    </header>

    <section class="receipt-grid">
        <div>
            <h1>Recibo de Submiss&atilde;o de Pedido</h1>

            <div class="meta">
                <strong>Tipo de Processo :</strong> {{ $receipt['process_type'] }}<br>
                <strong>Processo N&ordm; :</strong> {{ $receipt['process_number'] }}<br>
                <strong>Tipo de Solicita&ccedil;&atilde;o :</strong> {{ $receipt['request_type'] }}<br>
                <strong>Entidade :</strong> {{ $receipt['entity'] }}<br>
                <strong>Data de Entrada :</strong> {{ $receipt['entry_date'] }}<br>
                <strong>Requerente :</strong> {{ $receipt['applicant'] }}<br>
                <strong>NIF :</strong> {{ $receipt['nif'] }}
            </div>
        </div>

        <aside class="doc-box">
            <div class="doc-label">N&ordm; DOC</div>
            <div class="doc-count">{{ $receipt['document_count'] }}</div>
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
        <p class="section-title">Foram validados os seguintes requisitos :</p>

        <table>
            <thead>
            <tr>
                <th>Requisitos</th>
                <th>Obrigat&oacute;rios</th>
                <th>Sim</th>
                <th>N&atilde;o</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($receipt['requirements'] as $requirement)
                <tr>
                    <td>{{ $requirement['name'] }}</td>
                    <td>{!! $requirement['required'] ? 'Sim' : 'N&atilde;o' !!}</td>
                    <td><span class="check">{!! $requirement['fulfilled'] ? '&#10003;' : '' !!}</span></td>
                    <td><span class="check">{!! ! $requirement['fulfilled'] ? '&#10003;' : '' !!}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sem requisitos associados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
    @endif
</main>
</body>
</html>
