<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice['title'] ?? 'Fatura Pro Forma' }}</title>
    <style>
        :root {
            --blue: #005d91;
            --blue-strong: #004b78;
            --cyan: #17b7df;
            --ink: #163344;
            --muted: #456271;
            --line: #a9c5d2;
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
            font-size: 9px;
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
            height: 148px;
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
            padding: 0 40px 70px;
        }

        .institution {
            margin: -2px 8px 0 auto;
            width: 356px;
            text-align: right;
            line-height: 1.72;
        }

        .institution strong {
            display: inline-block;
            margin-bottom: 4px;
            color: var(--blue);
            font-weight: 800;
        }

        .client {
            margin-top: 38px;
            line-height: 1.55;
        }

        .invoice-title {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 34px;
            color: var(--blue);
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 10px;
            font-weight: 500;
        }

        .invoice-title span {
            color: var(--ink);
            font-size: 8px;
        }

        .rule {
            height: 1px;
            margin-top: 7px;
            background: var(--line);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 7px 0;
            text-align: left;
            vertical-align: top;
            font-size: 8px;
            font-weight: 400;
        }

        th {
            color: var(--ink);
        }

        .number {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .thin-table {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .thin-table + .thin-table {
            margin-top: 42px;
        }

        .items {
            margin-top: 4px;
            min-height: 92px;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.7fr 0.9fr;
            gap: 26px;
            align-items: start;
            margin-top: 20px;
        }

        .section-title {
            margin: 0 0 9px;
            font-size: 9px;
            font-weight: 700;
        }

        .tax-table {
            border-top: 1px solid var(--line);
        }

        .tax-table th,
        .tax-table td {
            border-bottom: 1px solid #d7e4ea;
        }

        .validation {
            text-align: center;
            word-break: break-word;
            line-height: 1.4;
        }

        .qr-code,
        .qr {
            display: grid;
            grid-template-columns: repeat(9, 1fr);
            width: 92px;
            height: 92px;
            margin: 10px auto 0;
            padding: 4px;
            gap: 2px;
            border: 1px solid #cad9df;
            background: #fff;
        }

        .qr-code {
            display: block;
            padding: 0;
        }

        .qr span {
            background: transparent;
        }

        .qr span:nth-child(1),
        .qr span:nth-child(2),
        .qr span:nth-child(3),
        .qr span:nth-child(10),
        .qr span:nth-child(12),
        .qr span:nth-child(19),
        .qr span:nth-child(20),
        .qr span:nth-child(21),
        .qr span:nth-child(7),
        .qr span:nth-child(8),
        .qr span:nth-child(9),
        .qr span:nth-child(16),
        .qr span:nth-child(18),
        .qr span:nth-child(25),
        .qr span:nth-child(26),
        .qr span:nth-child(27),
        .qr span:nth-child(55),
        .qr span:nth-child(56),
        .qr span:nth-child(57),
        .qr span:nth-child(64),
        .qr span:nth-child(66),
        .qr span:nth-child(73),
        .qr span:nth-child(74),
        .qr span:nth-child(75),
        .qr span:nth-child(5),
        .qr span:nth-child(14),
        .qr span:nth-child(23),
        .qr span:nth-child(29),
        .qr span:nth-child(31),
        .qr span:nth-child(33),
        .qr span:nth-child(36),
        .qr span:nth-child(39),
        .qr span:nth-child(41),
        .qr span:nth-child(43),
        .qr span:nth-child(46),
        .qr span:nth-child(48),
        .qr span:nth-child(50),
        .qr span:nth-child(53),
        .qr span:nth-child(59),
        .qr span:nth-child(61),
        .qr span:nth-child(68),
        .qr span:nth-child(70),
        .qr span:nth-child(77),
        .qr span:nth-child(79),
        .qr span:nth-child(81) {
            background: #1b2f3a;
        }

        .summary {
            border-top: 1px solid var(--line);
        }

        .summary-row {
            display: grid;
            grid-template-columns: 1fr 70px;
            gap: 14px;
            padding: 6px 0;
            border-bottom: 1px solid #d7e4ea;
        }

        .summary-row strong {
            font-weight: 700;
        }

        .transport-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 48px;
            margin-top: 72px;
            line-height: 1.55;
        }

        .transport-grid h2 {
            margin: 0 0 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--line);
            font-size: 9px;
            font-weight: 700;
        }

        .total {
            margin-top: 38px;
            display: grid;
            grid-template-columns: 1fr 70px;
            gap: 14px;
            font-weight: 700;
        }

        .footer-note {
            position: absolute;
            right: 40px;
            bottom: 34px;
            left: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            height: 29px;
            background: var(--footer);
            color: var(--muted);
            font-size: 9px;
        }

        .info-icon {
            display: inline-grid;
            place-items: center;
            width: 13px;
            height: 13px;
            border: 1px solid var(--ink);
            border-radius: 50%;
            color: var(--ink);
            font-size: 8px;
            font-weight: 700;
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
            color: var(--blue);
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
    @if ($error ?? false)
        <section class="error-state">
            <h1>N&atilde;o foi poss&iacute;vel carregar a fatura proforma</h1>
            <p>{{ $error }}</p>
        </section>
    @else
        @php
            if (! isset($headerImage)) {
                $headerImagePath = public_path('img/fatura-header.svg');
                $headerImage = file_exists($headerImagePath)
                    ? 'data:image/svg+xml;base64,'.base64_encode(file_get_contents($headerImagePath))
                    : null;
            }
        @endphp

        <header class="hero">
            @if ($headerImage)
                <img class="hero-image" src="{{ $headerImage }}" alt="">
            @endif
            <div class="brand" aria-label="ZEEMSV">
                <div class="brand-mark"></div>
                <div class="brand-name">ZEEMSV</div>
            </div>
        </header>

    <section class="content">
        <div class="institution">
            <strong>{{ $invoice['institution']['name'] }}</strong><br>
            Contribuinte N.&ordm;: {{ $invoice['institution']['nif'] }}<br>
            {{ $invoice['institution']['address'] }}<br>
            Email: {{ $invoice['institution']['email'] }}<br>
            Telef. {{ $invoice['institution']['phone'] }}
        </div>

        <section class="client">
            {{ $invoice['client']['salutation'] }}<br>
            <strong>{{ $invoice['client']['name'] }}</strong><br>
            {{ $invoice['client']['city'] }}<br>
            {{ $invoice['client']['country'] }}
        </section>

        <section class="invoice-title">
            <h1>{{ $invoice['title'] }}</h1>
            <span>{{ $invoice['type'] }}</span>
        </section>
        <div class="rule"></div>

        <table class="thin-table">
            <thead>
            <tr>
                <th>V.N.&ordm; Contrib.</th>
                <th>Requisi&ccedil;&atilde;o</th>
                <th class="center">Moeda</th>
                <th class="center">C&acirc;mbio</th>
                <th class="center">Data</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $invoice['meta']['nif'] }}</td>
                <td>{{ $invoice['meta']['requisition'] }}</td>
                <td class="center">{{ $invoice['meta']['currency'] }}</td>
                <td class="center">{{ $invoice['meta']['exchange'] }}</td>
                <td class="center">{{ $invoice['meta']['date'] }}</td>
            </tr>
            </tbody>
        </table>

        <table class="thin-table">
            <thead>
            <tr>
                <th>Desconto Comercial</th>
                <th>Desconto Adicional</th>
                <th>Vencimento</th>
                <th>Condi&ccedil;&atilde;o Pagamento</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $invoice['payment']['commercial_discount'] }}</td>
                <td>{{ $invoice['payment']['additional_discount'] }}</td>
                <td>{{ $invoice['payment']['due_date'] }}</td>
                <td>{{ $invoice['payment']['condition'] }}</td>
            </tr>
            </tbody>
        </table>

        <table class="items">
            <thead>
            <tr>
                <th style="width: 74px;">Artigo</th>
                <th>Descri&ccedil;&atilde;o</th>
                <th class="center" style="width: 48px;">Qtd.</th>
                <th class="center" style="width: 44px;">Un.</th>
                <th class="number" style="width: 76px;">Pr. Unit&aacute;rio</th>
                <th class="number" style="width: 58px;">Desc.</th>
                <th class="number" style="width: 52px;">IVA</th>
                <th class="number" style="width: 76px;">Valor</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoice['items'] as $item)
                <tr>
                    <td>{{ $item['article'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="center">{{ $item['quantity'] }}</td>
                    <td class="center">{{ $item['unit'] }}</td>
                    <td class="number">{{ $item['unit_price'] }}</td>
                    <td class="number">{{ $item['discount'] }}</td>
                    <td class="number">{{ $item['tax'] }}</td>
                    <td class="number">{{ $item['amount'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <section class="bottom-grid">
            <div>
                <p class="section-title">Quadro Resumo de Impostos</p>
                <table class="tax-table">
                    <thead>
                    <tr>
                        <th>Taxa/Valor:</th>
                        <th class="number">Incid./Qtd.</th>
                        <th class="number">Total:</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($invoice['taxes'] as $tax)
                        <tr>
                            <td>{{ $tax['rate'] }}</td>
                            <td class="number">{{ $tax['incidence'] }}</td>
                            <td class="number">{{ $tax['total'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="validation">
                {{ $invoice['validation_code'] }}
                @if (! empty($invoice['qr_code']))
                    <img class="qr-code" src="{{ $invoice['qr_code'] }}" alt="QR Code">
                @else
                    <div class="qr" aria-label="QR Code">
                        @for ($index = 0; $index < 81; $index++)
                            <span></span>
                        @endfor
                    </div>
                @endif
            </div>

            <div>
                <div class="summary">
                    @foreach ($invoice['summary'] as $label => $value)
                        <div class="summary-row">
                            <span>{{ $label }}:</span>
                            <span class="number">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="total">
                    <span>Total (CVE):</span>
                    <span class="number">{{ $invoice['total'] }}</span>
                </div>
            </div>
        </section>

        <section class="transport-grid">
            <div>
                <h2>{{ $invoice['loading']['origin_title'] }}</h2>
                {{ $invoice['loading']['origin_address'] }}<br>
                {{ $invoice['loading']['origin_city'] }}<br><br>
                {{ $invoice['loading']['origin_code'] }}
            </div>

            <div>
                <h2>{{ $invoice['loading']['destination_title'] }}</h2>
                {{ $invoice['loading']['destination_address'] }}<br>
                {{ $invoice['loading']['destination_city'] }}<br><br>
                {{ $invoice['loading']['destination_country'] }}
            </div>

            <div>
                @foreach ($invoice['bank_details'] as $line)
                    {{ $line }}<br>
                @endforeach
            </div>
        </section>
    </section>

    <footer class="footer-note">
        <span class="info-icon">i</span>
        <span>Documento sem efeitos fiscais</span>
    </footer>
    @endif
</main>
</body>
</html>
