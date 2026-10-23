<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salary Slip {{ $slipNo }}</title>
    <style>
        @page { margin: 8mm 10mm; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #17324a;
            font-size: 9pt;
            line-height: 1.25;
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
            max-height: 42px;
            max-width: 160px;
        }

        .doc-meta {
            text-align: right;
        }

        .eyebrow {
            margin: 0 0 2px;
            font-size: 7pt;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #738395;
            font-weight: 700;
        }

        .doc-title {
            margin: 0 0 1px;
            font-size: 18pt;
            line-height: 1;
            color: #0b4f79;
            font-weight: 700;
        }

        .doc-sub {
            margin: 0 0 6px;
            font-size: 8pt;
            color: #738395;
        }

        .meta-row {
            margin: 0 0 1px;
            font-size: 8pt;
            color: #17324a;
        }

        .meta-row span {
            color: #738395;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 6.5pt;
            font-weight: 700;
            margin-right: 5px;
        }

        .billed {
            margin: 10px 0 8px;
        }

        .billed-label {
            margin: 0 0 2px;
            font-size: 7pt;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #738395;
            font-weight: 700;
        }

        .billed-name {
            margin: 0 0 1px;
            font-size: 11pt;
            font-weight: 700;
            color: #0b4f79;
        }

        .billed-line {
            margin: 0;
            font-size: 8pt;
            color: #738395;
        }

        .items {
            margin: 0 0 8px;
            border: 1px solid #e6edf2;
            border-radius: 8px;
            overflow: hidden;
        }

        .items thead th {
            background: #073650;
            color: #fff;
            font-size: 7pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 6px 10px;
            text-align: left;
            font-weight: 700;
        }

        .items thead th.right,
        .items tbody td.right {
            text-align: right;
        }

        .items tbody td {
            padding: 6px 10px;
            border-top: 1px solid #e6edf2;
            vertical-align: middle;
            color: #17324a;
            font-size: 8.5pt;
        }

        .desc-main {
            font-weight: 700;
            color: #0b4f79;
        }

        .desc-sub {
            display: block;
            margin-top: 1px;
            font-size: 7.5pt;
            color: #738395;
            font-weight: 400;
        }

        .earn {
            color: #2f9e5f;
            font-weight: 700;
        }

        .deduct {
            color: #c24141;
            font-weight: 700;
        }

        .totals-wrap {
            width: 100%;
            margin: 0 0 8px;
        }

        .totals-wrap td {
            vertical-align: top;
            padding: 0;
        }

        .totals {
            width: 48%;
            margin-left: auto;
            border: 1px solid #e6edf2;
            border-radius: 8px;
            background: #f4f7f9;
            overflow: hidden;
        }

        .totals td {
            padding: 5px 10px;
            font-size: 8.5pt;
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
            font-size: 10pt;
            font-weight: 700;
            color: #0b4f79;
            border-top: 1px solid #e6edf2;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .section {
            margin: 0 0 8px;
            border: 1px solid #e6edf2;
            border-radius: 8px;
            overflow: hidden;
        }

        .section-head {
            background: #073650;
            color: #fff;
            font-size: 7pt;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 6px 10px;
            font-weight: 700;
        }

        .section-body table td {
            padding: 5px 10px;
            border-top: 1px solid #e6edf2;
            font-size: 8.5pt;
        }

        .section-body table tr:first-child td {
            border-top: 0;
        }

        .section-body .k {
            width: 34%;
            color: #738395;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
        }

        .section-body .v {
            color: #17324a;
            font-weight: 600;
        }

        .sign {
            width: 100%;
            margin-top: 14px;
        }

        .sign td {
            width: 50%;
            padding: 0 8px;
            vertical-align: bottom;
        }

        .sign-line {
            border-top: 1px solid #c5d0d8;
            margin-top: 22px;
            padding-top: 4px;
            text-align: center;
            font-size: 7.5pt;
            color: #738395;
        }

        .thanks {
            margin: 10px 0 0;
            text-align: center;
            font-size: 7.5pt;
            color: #738395;
        }
    </style>
</head>
<body>
    @php
        $staff = $slip->user;
        $period = $slip->monthLabel().' '.$slip->year;
        $gross = number_format((float) $slip->gross_amount, 2);
        $unpaidDays = number_format((float) $slip->unpaid_leave_days, 1);
        $unpaidDeduction = number_format((float) $slip->unpaid_leave_deduction, 2);
        $other = number_format((float) $slip->other_deductions, 2);
        $net = number_format((float) $slip->net_amount, 2);
        $perDay = number_format((float) $slip->per_day_deduction, 2);
        $totalDeductions = number_format(
            (float) $slip->unpaid_leave_deduction + (float) $slip->other_deductions,
            2
        );
    @endphp

    <table class="header">
        <tr>
            <td style="width: 48%;">
                @if ($logo)
                    <img class="logo" src="{{ $logo }}" alt="Federal Group">
                @else
                    <strong style="font-size: 12pt; color: #0b4f79;">Federal Manpower</strong>
                @endif
            </td>
            <td class="doc-meta" style="width: 52%;">
                <p class="eyebrow">Official accounts document</p>
                <p class="doc-title">SALARY SLIP</p>
                <p class="doc-sub">HR &amp; Payroll</p>
                <p class="meta-row"><span>Slip number</span>{{ $slipNo }}</p>
                <p class="meta-row"><span>Period</span>{{ $period }}</p>
                <p class="meta-row"><span>Generated</span>{{ $slip->created_at?->format('d M Y') ?? '—' }}</p>
                <p class="meta-row"><span>By</span>{{ $slip->generator?->name ?? '—' }}</p>
            </td>
        </tr>
    </table>

    <div class="billed">
        <p class="billed-label">Employee :</p>
        <p class="billed-name">{{ $staff?->name ?? '—' }}</p>
        @if ($staff?->email)
            <p class="billed-line">{{ $staff->email }}</p>
        @endif
        @if ($staff?->role)
            <p class="billed-line">Role: {{ $staff->roleLabel() }}</p>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 62%;">Description</th>
                <th class="right" style="width: 38%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="desc-main">Gross salary</span>
                    <span class="desc-sub">Basic pay for {{ $period }}</span>
                </td>
                <td class="right earn">Rs {{ $gross }}</td>
            </tr>
            <tr>
                <td>
                    <span class="desc-main">Unpaid leave deduction</span>
                    <span class="desc-sub">{{ $unpaidDays }} day(s) × Rs {{ $perDay }}</span>
                </td>
                <td class="right deduct">− Rs {{ $unpaidDeduction }}</td>
            </tr>
            <tr>
                <td>
                    <span class="desc-main">Other deductions</span>
                    @if ($slip->notes)
                        <span class="desc-sub">{{ $slip->notes }}</span>
                    @endif
                </td>
                <td class="right deduct">− Rs {{ $other }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals-wrap">
        <tr>
            <td style="width: 52%;"></td>
            <td style="width: 48%;">
                <table class="totals">
                    <tr>
                        <td class="label">Gross</td>
                        <td class="value">Rs {{ $gross }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total deductions</td>
                        <td class="value">Rs {{ $totalDeductions }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">Net pay (PKR)</td>
                        <td class="value">Rs {{ $net }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-head">Summary</div>
        <div class="section-body">
            <table>
                <tr>
                    <td class="k">Unpaid leave days</td>
                    <td class="v">{{ $unpaidDays }}</td>
                </tr>
                <tr>
                    <td class="k">Per day deduction</td>
                    <td class="v">Rs {{ $perDay }}</td>
                </tr>
                <tr>
                    <td class="k">Net payable</td>
                    <td class="v">Rs {{ $net }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="sign" style="margin-top: 20%;">
        <tr>
            <td><div class="sign-line">Employee signature</div></td>
            <td><div class="sign-line">Authorized signature</div></td>
        </tr>
    </table>

</body>
</html>
