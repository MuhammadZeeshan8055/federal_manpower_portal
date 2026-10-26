<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bio Data — {{ $client->full_name }}</title>
    <style>
        @page { margin: 6mm 10.5mm 5.5mm; }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #202996;
            font-size: 8pt;
            line-height: 1.15;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sheet {
            position: relative;
            width: auto;
            margin: 0 3mm;
            padding: 1.5mm 2mm 1mm;
        }

        .content {
            position: relative;
            z-index: 2;
        }

        .watermark {
            position: absolute;
            z-index: 1;
            top: 96mm;
            left: 50%;
            width: 105mm;
            margin-left: -52.5mm;
            opacity: 0.045;
        }

        .header {
            height: 32mm;
        }

        .header td {
            padding: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 22%;
            padding-left: 1.5mm !important;
            text-align: left;
        }

        .logo {
            display: block;
            width: 40mm;
            max-height: 28mm;
            object-fit: contain;
        }

        .brand-cell {
            width: 46%;
            padding: 0 2mm !important;
            text-align: center;
        }

        .brand-title {
            color: #202996;
            font-size: 13.2pt;
            font-weight: 800;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .brand-subtitle {
            margin-top: 2.2mm;
            font-size: 10pt;
            font-weight: 700;
        }

        .brand-licence {
            margin-top: 2.3mm;
            font-size: 8.2pt;
            font-weight: 700;
            letter-spacing: .06em;
        }

        .qr-cell {
            width: 14%;
            padding: 0 1mm !important;
            text-align: center;
            vertical-align: middle;
        }

        .qr-box {
            display: inline-block;
            width: 28mm;
            height: 28mm;
            overflow: hidden;
            text-align: center;
            vertical-align: middle;
            background: #fff;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .qr-caption {
            margin-top: .6mm;
            color: #5c6b8a;
            font-size: 5.5pt;
            font-weight: 700;
            letter-spacing: .04em;
            text-align: center;
        }

        .photo-cell {
            width: 18%;
            padding-right: 1.5mm !important;
            text-align: right;
        }

        .photo-box {
            display: inline-block;
            width: 31.5mm;
            height: 31mm;
            overflow: hidden;
            border: 1.4pt solid #10265b;
            text-align: center;
            vertical-align: middle;
            margin-top: 10%;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            display: block;
            padding-top: 13mm;
            color: #9aa4bb;
            font-size: 7pt;
            letter-spacing: .12em;
        }

        .bio-title {
            margin: 1.5mm 0 .4mm;
            text-align: center;
        }

        .bio-title span {
            display: inline-block;
            padding: .4mm .8mm .55mm;
            color: #fff;
            background: #f47b20;
            font-size: 11.5pt;
            font-weight: 800;
            line-height: 1;
        }

        .job-row {
            margin-bottom: .7mm;
        }

        .job-row td {
            padding: 0 0 .5mm;
            vertical-align: bottom;
            font-size: 8.5pt;
        }

        .job-label {
            width: 12%;
            font-weight: 800;
            white-space: nowrap;
        }

        .job-value {
            width: 50%;
            padding-left: 1mm !important;
            border-bottom: 2px solid #202996;
            color: #17264d;
        }

        .job-country-label {
            width: 12%;
            padding-left: 4mm !important;
            font-weight: 800;
            white-space: nowrap;
        }

        .job-country-value {
            width: 26%;
            padding-left: 1mm !important;
            border-bottom: 2px solid #202996;
            color: #17264d;
        }

        .form-grid,
        .experience,
        .signatures,
        .interview-grid {
            border: 2px solid #202996;
        }

        .form-grid td,
        .experience th,
        .experience td,
        .signatures td,
        .interview-grid td {
            border: 2px solid #202996;
            border-top: none;
        }

        .form-grid td {
            height: 6.3mm;
            padding: .65mm 1.4mm;
            vertical-align: middle;
        }

        .form-grid .compact td,
        .form-grid tr.compact > td {
            height: 6mm;
        }

        .label {
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .value {
            color: #17264d;
            font-size: 7.6pt;
            font-weight: 400;
            overflow-wrap: break-word;
        }

        .section-label {
            text-align: left;
            vertical-align: middle !important;
            white-space: normal;
            line-height: 1.65;
        }

        .experience-title {
            height: 6mm;
            padding: 1.6mm 2mm .7mm;
            border: 2px solid #202996;
            border-top: 0;
            border-bottom: 0;
            font-weight: 800;
        }

        .experience-title span {
            color: #17264d;
            font-weight: 400;
        }

        .experience th {
            height: 4.8mm;
            padding: .6mm;
            font-size: 6.8pt;
            font-weight: 800;
            text-align: center;
        }

        .experience td {
            height: 5mm;
            padding: .55mm 1mm;
            color: #17264d;
            font-size: 7pt;
        }

        .signatures {
            border-top: 0;
        }

        .signatures td {
            height: 14mm;
            padding: 8mm 5mm 1mm;
            text-align: center;
            vertical-align: bottom;
            font-size: 6.6pt;
            font-weight: 800;
        }

        .signature-line {
            width: 60%;
            margin: 0 auto 1mm;
            border-top: 1px dotted #202996;
        }

        .interview-title {
            height: 4.8mm;
            padding-top: 1.1mm;
            border: 2px solid #202996;
            border-top: 0;
            text-align: center;
            font-size: 7pt;
            font-weight: 800;
        }

        .interview-grid {
            border-top: 0;
        }

        .interview-grid td {
            height: 5.2mm;
            padding: .65mm 1.7mm;
            vertical-align: middle;
        }

        .selection-row td {
            height: 5.4mm;
        }

        .choice-label {
            text-align: center;
            font-size: 6.7pt;
            font-weight: 800;
        }

        .checkbox {
            width: 5.6mm;
            padding: 0 !important;
        }

        .remarks-row td {
            height: 10mm;
            vertical-align: top;
        }

        .footer {
            margin-top: 3mm;
            color: #202996;
            font-size: 7.6pt;
            font-weight: 700;
            table-layout: fixed;
        }

        .footer td {
            padding: 0.6mm 1.2mm;
            vertical-align: middle;
            line-height: 1.35;
        }

        .footer--top td {
            padding-bottom: 1.2mm;
        }

        .footer--contact {
            margin-top: 0.4mm;
        }

        .footer-icon {
            display: inline-block;
            width: 3.4mm;
            height: 3.4mm;
            vertical-align: middle;
            margin: 0 1.4mm 0 0;
        }

        .footer-left {
            width: 66%;
            padding-right: 3mm !important;
        }

        .footer-right {
            width: 34%;
            text-align: right;
            padding-left: 2mm !important;
            white-space: nowrap;
        }

        .footer-contact {
            width: 33.33%;
            white-space: nowrap;
        }

        .footer-contact--end {
            text-align: right;
        }
    </style>
</head>
<body>
@php
    // Show text, or a blank space if empty (keeps table cells from collapsing)
    $show = function ($value) {
        if ($value === null || $value === '') {
            return '&nbsp;';
        }

        return e($value);
    };

    $showDate = function ($value) {
        if (! $value) {
            return '&nbsp;';
        }

        return e($value->format('d/m/Y'));
    };

    $jobTitle = $client->trade?->name;
    $country = $client->country?->name;
    $company = $client->company?->name;
    $careOff = $client->careOff?->name;
@endphp

<div class="sheet">
    @if ($logo)
        <img class="watermark" src="{{ $logo }}" alt="">
    @endif

    <div class="content">
        <table class="header">
            <tr>
                <td class="logo-cell">
                    @if ($logo)
                        <img class="logo" src="{{ $logo }}" alt="Federal Manpower Promoter">
                    @elseif ($mark)
                        <img class="logo" src="{{ $mark }}" alt="Federal Manpower Promoter">
                    @endif
                </td>
                <td class="brand-cell">
                    <div class="brand-title">FEDERAL MANPOWER PROMOTER</div>
                    <div class="brand-subtitle">Overseas Employment Promoters</div>
                    <div class="brand-licence">LICENCE NO. OP&amp;HRD/4749/RWP</div>
                </td>
                <td class="qr-cell">
                    @if (! empty($qr))
                        <div class="qr-box">
                            <img src="{{ $qr }}" alt="Status QR">
                        </div>
                        <div class="qr-caption">SCAN STATUS</div>
                    @endif
                </td>
                <td class="photo-cell">
                    <div class="photo-box">
                        @if ($photo)
                            <img src="{{ $photo }}" alt="Candidate photo">
                        @else
                            <span class="photo-placeholder">PHOTO</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="bio-title"><span>BIO DATA</span></div>

        <table class="job-row">
            <tr>
                <td class="job-label">JOB TITLE</td>
                <td class="job-value">{!! $show($jobTitle) !!}</td>
                <td class="job-country-label">COUNTRY</td>
                <td class="job-country-value">{!! $show($country) !!}</td>
            </tr>
        </table>

        <table class="form-grid">
            <colgroup>
                <col style="width:17.2%">
                <col style="width:13.5%">
                <col style="width:11%">
                <col style="width:8%">
                <col style="width:14.8%">
                <col style="width:16.8%">
                <col style="width:18.7%">
            </colgroup>
            <tr>
                <td class="label">NAME</td>
                <td class="value" colspan="4">{!! $show($client->full_name) !!}</td>
                <td class="label">HGT</td>
                <td class="value">{!! $show($client->height) !!}</td>
            </tr>
            <tr>
                <td class="label">FATHER'S NAME</td>
                <td class="value" colspan="4">{!! $show($client->father_name) !!}</td>
                <td class="label">WGT</td>
                <td class="value">{!! $show($client->weight) !!}</td>
            </tr>
            <tr>
                <td class="label">MOTHER'S NAME</td>
                <td class="value" colspan="4">{!! $show($client->mother_name) !!}</td>
                <td class="label">GENDER</td>
                <td class="value">{!! $show($client->gender ? ucfirst($client->gender) : null) !!}</td>
            </tr>
            <tr>
                <td class="label section-label" rowspan="3">PERMANENT ADDRESS</td>
                <td class="value" colspan="4" rowspan="2">{!! $show($client->address) !!}</td>
                <td class="label">DATE OF BIRTH</td>
                <td class="value">{!! $showDate($client->date_of_birth) !!}</td>
            </tr>
            <tr>
                <td class="label">AGE</td>
                <td class="value">{!! $show($client->age) !!}</td>
            </tr>
            <tr class="compact">
                <td class="label">POLICE STATION</td>
                <td class="value">{!! $show($client->police_station) !!}</td>
                <td class="label">DISTRICT</td>
                <td class="value">{!! $show($client->district) !!}</td>
                <td class="label">MARITAL STATUS</td>
                <td class="value">{!! $show($client->marital_status ? ucfirst($client->marital_status) : null) !!}</td>
            </tr>
            <tr>
                <td class="label section-label" rowspan="3">PASSPORT<br>DETAILS</td>
                <td class="label">P.P NO</td>
                <td class="value" colspan="3">{!! $show($client->passport_number) !!}</td>
                <td class="label">RELIGION</td>
                <td class="value">{!! $show($client->religion) !!}</td>
            </tr>
            <tr class="compact">
                <td class="label">DATE OF ISSUE</td>
                <td class="value">{!! $showDate($client->passport_issue_date) !!}</td>
                <td class="label">DATE OF EXPIRY</td>
                <td class="value">{!! $showDate($client->passport_expiry) !!}</td>
                <td class="label">MEDICAL FITNESS</td>
                <td class="value">{!! $show($client->medical_fitness ? ucfirst($client->medical_fitness) : null) !!}</td>
            </tr>
            <tr>
                <td class="label">PLACE OF BIRTH</td>
                <td class="value" colspan="3">{!! $show($client->place_of_birth) !!}</td>
                <td class="label">C.O</td>
                <td class="value">{!! $show($careOff) !!}</td>
            </tr>
            <tr>
                <td class="label section-label" rowspan="3">EDUCATION</td>
                <td class="label">DEGREE</td>
                <td class="value" colspan="3">{!! $show($client->degree) !!}</td>
                <td class="label">ID NUMBER</td>
                <td class="value">{!! $show($client->cnic) !!}</td>
            </tr>
            <tr>
                <td class="label">COMPLETION YEAR</td>
                <td class="value" colspan="3">{!! $show($client->degree_year) !!}</td>
                <td class="label">CERTIFICATION</td>
                <td class="value">{!! $show($client->certification) !!}</td>
            </tr>
            <tr>
                <td class="label">BOARD/UNI</td>
                <td class="value" colspan="3">{!! $show($client->board_university) !!}</td>
                <td class="label">LANGUAGES</td>
                <td class="value">{!! $show($client->languages) !!}</td>
            </tr>
        </table>

        <div class="experience-title">TOTAL EXPERIENCE: <span>{{ $client->total_experience ?: '—' }}</span></div>
        <table class="experience">
            <colgroup>
                <col style="width:21.5%">
                <col style="width:18.7%">
                <col style="width:24.2%">
                <col style="width:16.8%">
                <col style="width:18.8%">
            </colgroup>
            <tr>
                <th>FROM - TO</th>
                <th>COMPANY</th>
                <th>PROJECT</th>
                <th>COUNTRY</th>
                <th>JOB TITLE</th>
            </tr>
            @php $experienceRows = $client->experience_rows ?? []; @endphp
            @for ($i = 0; $i < 5; $i++)
                @php $row = $experienceRows[$i] ?? []; @endphp
                <tr>
                    <td>{!! $show($row['from_to'] ?? null) !!}</td>
                    <td>{!! $show($row['company'] ?? null) !!}</td>
                    <td>{!! $show($row['project'] ?? null) !!}</td>
                    <td>{!! $show($row['country'] ?? null) !!}</td>
                    <td>{!! $show($row['job_title'] ?? null) !!}</td>
                </tr>
            @endfor
        </table>

        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    SIGNATURE OF CANDIDATE
                </td>
                <td>
                    <div class="signature-line"></div>
                    SIGNATURE OF INTERVIEWER
                </td>
            </tr>
        </table>

        <div class="interview-title">INTERVIEW DETAILS</div>
        <table class="interview-grid">
            <colgroup>
                <col style="width:21.5%">
                <col style="width:12.8%">
                <col style="width:4%">
                <col style="width:17.8%">
                <col style="width:33.8%">
                <col style="width:4%">
                <col style="width:6.1%">
            </colgroup>
            <tr>
                <td class="label">COMPANY</td>
                <td class="value" colspan="6">{!! $show($company) !!}</td>
            </tr>
            <tr>
                <td class="label">PROJECT</td>
                <td class="value" colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td class="label">COUNTRY</td>
                <td class="value" colspan="6">{!! $show($country) !!}</td>
            </tr>
            <tr class="selection-row">
                <td class="label">SELECTION STATUS</td>
                <td class="choice-label">SELECTED</td>
                <td class="checkbox">&nbsp;</td>
                <td class="value">&nbsp;</td>
                <td class="choice-label">NOT SELECTED</td>
                <td class="checkbox">&nbsp;</td>
                <td class="value">&nbsp;</td>
            </tr>
            <tr class="remarks-row">
                <td class="label">SELECTION DATE<br><br>REMARKS</td>
                <td class="value" colspan="6">&nbsp;</td>
            </tr>
        </table>

        <table class="footer footer--top">
            <tr>
                <td class="footer-left">
                    <img class="footer-icon" src="{{ $iconLocation }}" alt="">Office No. 307, Talha Heights, 6th Rd, Satellite Town, Rawalpindi, 46000
                </td>
                <td class="footer-right">
                    <img class="footer-icon" src="{{ $iconGlobe }}" alt="">www.federalmanpower.com
                </td>
            </tr>
        </table>
        <table class="footer footer--contact">
            <tr>
                <td class="footer-contact">
                    <img class="footer-icon" src="{{ $iconLandline }}" alt="">+92 51 2716031
                </td>
                <td class="footer-contact">
                    <img class="footer-icon" src="{{ $iconPhone }}" alt="">+92 300 4340666 &nbsp; +92 302 4340666 &nbsp; +92 304 4340666 &nbsp; +92 330 1000069
                </td>
                <td class="footer-contact footer-contact--end">
                    <img class="footer-icon" src="{{ $iconMail }}" alt="">info@federalmanpower.com
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
