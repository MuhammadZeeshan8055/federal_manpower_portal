<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receiptNo }}</title>
    <style>
        @page { margin: 14mm 14mm 12mm; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #17324a;
            font-size: 10pt;
            line-height: 1.4;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
            padding: 0;
        }

        .logo {
            max-height: 58px;
            max-width: 200px;
        }

        .doc-meta {
            text-align: right;
        }

        .eyebrow {
            margin: 0 0 4px;
            font-size: 8pt;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #738395;
            font-weight: 700;
        }

        .doc-title {
            margin: 0 0 2px;
            font-size: 26pt;
            line-height: 1;
            color: #0b4f79;
            font-weight: 700;
        }

        .doc-sub {
            margin: 0 0 12px;
            font-size: 9pt;
            color: #738395;
        }

        .meta-row {
            margin: 0 0 3px;
            font-size: 9pt;
            color: #17324a;
        }

        .meta-row span {
            color: #738395;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 7.5pt;
            font-weight: 700;
            margin-right: 6px;
        }

        .billed {
            margin: 22px 0 18px;
        }

        .billed-label {
            margin: 0 0 4px;
            font-size: 8pt;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #738395;
            font-weight: 700;
        }

        .billed-name {
            margin: 0 0 3px;
            font-size: 12pt;
            font-weight: 700;
            color: #0b4f79;
        }

        .billed-line {
            margin: 0;
            font-size: 9pt;
            color: #738395;
        }

        .items {
            margin: 0 0 14px;
            border: 1px solid #e6edf2;
            border-radius: 10px;
            overflow: hidden;
        }

        .items thead th {
            background: #073650;
            color: #fff;
            font-size: 8pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
        }

        .items thead th.right,
        .items tbody td.right {
            text-align: right;
        }

        .items thead th.center,
        .items tbody td.center {
            text-align: center;
        }

        .items tbody td {
            padding: 12px;
            border-top: 1px solid #e6edf2;
            vertical-align: middle;
            color: #17324a;
            font-size: 9.5pt;
        }

        .desc-main {
            font-weight: 700;
            color: #0b4f79;
        }

        .desc-sub {
            display: block;
            margin-top: 2px;
            font-size: 8.5pt;
            color: #738395;
            font-weight: 400;
        }

        .qty-pill {
            display: inline-block;
            min-width: 28px;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e8f4fb;
            color: #0b4f79;
            font-weight: 700;
            font-size: 9pt;
        }

        .totals-wrap {
            width: 100%;
            margin: 0 0 18px;
        }

        .totals-wrap td {
            vertical-align: top;
            padding: 0;
        }

        .totals {
            width: 52%;
            margin-left: auto;
            border: 1px solid #e6edf2;
            border-radius: 10px;
            background: #f4f7f9;
            overflow: hidden;
        }

        .totals td {
            padding: 8px 12px;
            font-size: 9.5pt;
        }

        .totals .label {
            color: #738395;
        }

        .totals .value {
            text-align: right;
            color: #17324a;
            font-weight: 600;
        }

        .totals .total-row .label,
        .totals .total-row .value {
            font-size: 11pt;
            font-weight: 700;
            color: #0b4f79;
            border-top: 1px solid #e6edf2;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .section {
            margin: 0 0 16px;
            border: 1px solid #e6edf2;
            border-radius: 10px;
            overflow: hidden;
        }

        .section-head {
            background: #073650;
            color: #fff;
            font-size: 8pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 10px 12px;
            font-weight: 700;
        }

        .section-body {
            padding: 0;
        }

        .section-body table td {
            padding: 9px 12px;
            border-top: 1px solid #e6edf2;
            font-size: 9.5pt;
        }

        .section-body table tr:first-child td {
            border-top: 0;
        }

        .section-body .k {
            width: 34%;
            color: #738395;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
        }

        .section-body .v {
            color: #17324a;
            font-weight: 600;
        }

        .summary {
            width: 100%;
            margin-top: 8px;
        }

        .summary td {
            padding: 4px 2px;
            font-size: 10pt;
        }

        .summary .right {
            text-align: right;
        }

        .received {
            color: #2f9e5f;
            font-weight: 700;
        }

        .balance {
            color: #0b4f79;
            font-weight: 700;
        }

        .sign {
            width: 100%;
            margin-top: 28px;
        }

        .sign td {
            width: 50%;
            padding: 0 10px;
            vertical-align: bottom;
        }

        .sign-line {
            border-top: 1px solid #c5d0d8;
            margin-top: 40px;
            padding-top: 6px;
            text-align: center;
            font-size: 8.5pt;
            color: #738395;
        }

        .thanks {
            margin: 20px 0 0;
            text-align: center;
            font-size: 8.5pt;
            color: #738395;
        }
    </style>
</head>
<body>
    @php
        $client = $payment->client;
        $amount = number_format((float) $payment->amount, 2);
        $methodName = $payment->method?->name ?? '—';
        $accountLabel = $payment->account?->label() ?? '—';
        $description = 'Payment received — '.$methodName;
        if ($payment->notes) {
            $descriptionNote = $payment->notes;
        } else {
            $descriptionNote = 'Received in '.$accountLabel;
        }
    @endphp

    <table class="header">
        <tr>
            <td style="width: 48%;">
                @if ($logo)
                    <img class="logo" src="{{ $logo }}" alt="Federal Group">
                @else
                    <strong style="font-size: 14pt; color: #0b4f79;">Federal Manpower</strong>
                @endif
            </td>
            <td class="doc-meta" style="width: 52%;">
                <p class="eyebrow">Official accounts document</p>
                <p class="doc-title">RECEIPT</p>
                <p class="doc-sub">Accounts &amp; Billing</p>
                <p class="meta-row"><span>Receipt number</span>{{ $receiptNo }}</p>
                <p class="meta-row"><span>Receipt date</span>{{ $payment->paid_on->format('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="billed">
        <p class="billed-label">Received from :</p>
        <p class="billed-name">{{ $client?->full_name ?? '—' }}</p>
        @if ($client?->phone)
            <p class="billed-line">{{ $client->phone }}</p>
        @endif
        @if ($client?->cnic)
            <p class="billed-line">CNIC: {{ $client->cnic }}</p>
        @endif
        @if ($client?->passport_number)
            <p class="billed-line">Passport: {{ $client->passport_number }}</p>
        @endif
        @if ($client?->address)
            <p class="billed-line">{{ $client->address }}</p>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 48%;">Description</th>
                <th class="center" style="width: 12%;">Qty</th>
                <th class="right" style="width: 20%;">Price</th>
                <th class="right" style="width: 20%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="desc-main">{{ $description }}</span>
                    <span class="desc-sub">{{ $descriptionNote }}</span>
                </td>
                <td class="center"><span class="qty-pill">1</span></td>
                <td class="right">Rs {{ $amount }}</td>
                <td class="right">Rs {{ $amount }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals-wrap">
        <tr>
            <td style="width: 48%;"></td>
            <td style="width: 52%;">
                <table class="totals">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rs {{ $amount }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">Total (PKR)</td>
                        <td class="value">Rs {{ $amount }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-head">Payment details</div>
        <div class="section-body">
            <table>
                <tr>
                    <td class="k">Method</td>
                    <td class="v">{{ $methodName }}</td>
                </tr>
                <tr>
                    <td class="k">Received in</td>
                    <td class="v">{{ $accountLabel }}</td>
                </tr>
                <tr>
                    <td class="k">Received by</td>
                    <td class="v">{{ $payment->recorder?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Paid on</td>
                    <td class="v">{{ $payment->paid_on->format('d M Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td></td>
            <td class="right received">Total received &nbsp; Rs {{ $amount }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="right balance">Balance due &nbsp; Rs 0.00</td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td><div class="sign-line">Client signature</div></td>
            <td><div class="sign-line">Authorized signature</div></td>
        </tr>
    </table>

    <p class="thanks">Thank you for your payment.</p>
</body>
</html>
