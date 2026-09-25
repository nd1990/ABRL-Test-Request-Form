<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 9mm 9mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            color: #1a1a1a;
            font-size: 8pt;
            line-height: 1.33;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        /* ---------- Base table ---------- */
        table.cell-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.cell-table td, table.cell-table th { padding: 3pt 4pt; vertical-align: top; }

        /* Uniform 1px black borders throughout (max 1.5px) */
        .bordered td, .bordered th { border: 1px solid #d3d3d3; }

        /* ---------- HEADER ---------- */
        .header-banner { border: 1px solid #d3d3d3; }
        .header-row { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .header-row td { vertical-align: middle; border: none; padding: 7pt; }
        .header-logo { width: 20%; text-align: left; padding-left: 10pt; }
        .header-logo img { max-height: 62pt; max-width: 100%; }
        .header-text { width: 60%; text-align: center; }
        .header-right { width: 20%; }
        .lab-name { font-size: 13pt; font-weight: bold; color: #1a1a1a; letter-spacing: 0.5pt; }
        .lab-web { font-size: 8.5pt; font-weight: bold; color: #555555; margin-top: 2pt; }
        .form-title { font-size: 9.5pt; font-weight: bold; color: #2b2b2b; margin-top: 2pt; }
        .form-ref { font-size: 7pt; font-weight: bold; color: #555555; margin-top: 2pt; }

        /* ---------- Invoice No / Date bar ---------- */
        .bar { width: 100%; margin-top: 3pt; border: 1px solid #d3d3d3; border-collapse: collapse; table-layout: fixed; }
        .bar td { background-color: #eef0f3; padding: 3.5pt 5pt; font-size: 8pt; border: 1px solid #d3d3d3; }
        .bar td.k { font-weight: bold; background-color: #ffffff; width: 23%; }

        /* ---------- Section titles ---------- */
        .section-title { text-align: center; font-weight: bold; text-transform: uppercase; font-size: 11pt; margin: 5.5pt 0 2.5pt 0; color: #1a1a1a; }
        .rule { display: block; margin-top: 2pt; text-align: center; }
        .rule .line { display: inline-block; width: 55pt; height: 0; border-top: 1px solid #000000; vertical-align: middle; }
        .rule .dmd { display: inline-block; width: 5pt; height: 5pt; margin: 0 6pt; background-color: #000000; vertical-align: middle; }

        /* ---------- Party ---------- */
        .party td, .party th { font-size: 7.5pt; }
        .party td.label { font-weight: bold; width: 40%; background-color: #eef0f3; border-right: 1px solid #d3d3d3; }
        .bordered.party td { border: 1px solid #d3d3d3; }

        /* ---------- Table headers ---------- */
        .thead td, .thead th { background-color: #3a3f47; color: #ffffff; font-weight: bold; font-size: 7pt; }

        .sample-head td, .sample-head th { font-size: 7.5pt; }

        .inv-head th { font-size: 7pt; line-height: 1.3; }
        .inv-row td { padding: 2pt 3pt; font-size: 7pt; }
        .inv-row:nth-child(even) td { background-color: #f7f8fa; }
        .inv-row td.sn { text-align: center; }
        .qty { text-align: center; }
        .charges { text-align: right; }

        /* ---------- Totals ---------- */
        .totals { width: 100%; margin-top: 2pt; border-collapse: collapse; page-break-inside: avoid; }
        .totals td { padding: 3.5pt 5pt; font-size: 8pt; border: 1px solid #d3d3d3; }
        .totals td.lbl { background-color: #eef0f3; width: 55%; }
        .totals tr.grand td { background-color: #2b2b2b; color: #ffffff; font-weight: bold; font-size: 8.5pt; }
        .totals td.amnt { text-align: right; font-weight: bold; background-color: #f7f8fa; }

        .amount-words { width: 100%; margin-top: 2pt; border: 1px solid #d3d3d3; border-collapse: collapse; page-break-inside: avoid; }
        .amount-words td { padding: 3.5pt 5pt; font-size: 8pt; }
        .amount-words td.k { background-color: #eef0f3; font-weight: bold; width: 25%; }

        /* ---------- PAGE 2 ---------- */
        .page2 { page-break-before: always; }

        .h2 { text-align: center; font-weight: bold; text-transform: uppercase; font-size: 11pt; color: #1a1a1a; margin: 8pt 0 4pt 0; }

        .bank { width: 100%; border-collapse: collapse; }
        .bank td { padding: 3.5pt 5pt; border: 1px solid #d3d3d3; font-size: 8pt; }
        .bank td.k { font-weight: bold; width: 20%; background-color: #eef0f3; }
        .bank td.v { width: 30%; }

        .terms-title { text-align: center; font-weight: bold; text-transform: uppercase; font-size: 11pt; margin: 8pt 0 4pt 0; }

        .terms-row { padding: 2.5pt 3pt 2.5pt 6pt; font-size: 8.5pt; line-height: 1.35; text-align: justify; }
        .terms-row .num { font-weight: bold; }
    </style>
</head>
<body>

@php
    $currency = $currency ?? '₹';
    $inv = $invoice;
    $idate = $inv->invoice_date ? $inv->invoice_date->format('d-m-Y') : '';
    $clientCompany = $inv->company_name ?: $inv->client_name;
    $clientAddr = trim(implode(', ', array_filter([$inv->address, $inv->address_line2, $inv->city, $inv->state, $inv->postal_code, $inv->country])));
    $courierAddr = $inv->courier_address
        ? trim(implode(', ', array_filter([$inv->courier_address, $inv->courier_address_line2, $inv->courier_city, $inv->courier_state, $inv->courier_postal_code, $inv->courier_country])))
        : '';
    $taxPct = $inv->subtotal > 0 ? (round(((float)$inv->tax / (float)$inv->subtotal) * 100)) : 0;
    $currencyFormat = fn($n) => $currency . number_format((float) $n, 2);
    $companyPhone = $company['phone'] ?? '';
    $companyName = $company['name'] ?? 'Agri Biochem Research Lab';
    $companyWebsite = $company['website'] ?? '';
    $companyAddress = $company['address'] ?? '';
@endphp

<!-- ================= PAGE 1 ================= -->
<div class="header-banner">
    <table class="header-row">
        <tr>
            <td class="header-logo">
                @php
                    $logoPath = $company['logo_path'] ?? public_path('images/abrl-logo.png');
                @endphp
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="ABRL Logo">
                @endif
            </td>
            <td class="header-text">
                <div class="lab-name">AGRI BIOCHEM RESEARCH LAB (ABRL)</div>
                <div class="lab-web">{{ $companyWebsite }}</div>
                <div class="form-title">FINAL TAX INVOICE</div>
                <div class="form-ref">F/CSD/06&nbsp;&nbsp;|&nbsp;&nbsp;Issue No. 02</div>
            </td>
            <td class="header-right"></td>
        </tr>
    </table>
</div>

<!-- Invoice No. / Date -->
<table class="bar">
    <tr>
        <td class="k" style="width:23%">Invoice No.</td>
        <td style="width:27%">{{ $inv->invoice_number }}</td>
        <td class="k" style="width:23%">Invoice Date</td>
        <td style="width:27%">{{ $idate }}</td>
    </tr>
    @if($inv->quotation)
    <tr>
        <td class="k" style="width:23%">Reference Quotation</td>
        <td style="width:27%">{{ $inv->quotation->quotation_number }}</td>
        <td class="k" style="width:23%">Quotation Date</td>
        <td style="width:27%">{{ $inv->quotation->quotation_date ? $inv->quotation->quotation_date->format('d-m-Y') : '' }}</td>
    </tr>
    @endif
</table>

<!-- Party details -->
<table class="cell-table party bordered" style="margin-top:2pt">
    <tr>
        <td class="label">Company Name and Address</td>
        <td>
            <div>{{ $clientCompany }}</div>
            @if($clientAddr)<div>{{ $clientAddr }}</div>@endif
        </td>
    </tr>
    <tr>
        <td class="label">Contact Person Name</td>
        <td>{{ $inv->client_name }}</td>
    </tr>
    <tr>
        <td class="label">Mob No.</td>
        <td>{{ $inv->phone }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td>{{ $inv->email }}</td>
    </tr>
    <tr>
        <td class="label">GST Number</td>
        <td>{{ $inv->gst_number }}</td>
    </tr>
    @if($inv->pan_number)
    <tr>
        <td class="label">PAN Number</td>
        <td>{{ $inv->pan_number }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Address on Report and Invoice</td>
        <td>{{ $clientAddr }}</td>
    </tr>
    <tr>
        <td class="label">Address for courier of Report and Invoice</td>
        <td>{{ $courierAddr ?: $clientAddr }}</td>
    </tr>
</table>

<!-- SAMPLE DETAILS -->
<div class="section-title sm">SAMPLE DETAILS<span class="rule"><span class="line"></span><span class="dmd"></span><span class="line"></span></span></div>
<table class="cell-table sample-head bordered">
    <thead>
        <tr class="thead">
            <th style="width:5%">Sr No</th>
            <th style="width:25%">Name of Sample</th>
            <th style="width:20%">Sample Batch No.</th>
            <th style="width:24%">Sample physical form</th>
            <th style="width:26%">Specific Storage Condition</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="center">1</td>
            <td>{{ $inv->quotation?->sample_name }}</td>
            <td>{{ $inv->quotation?->sample_batch_no }}</td>
            <td>{{ $inv->quotation?->sample_physical_form }}</td>
            <td>{{ $inv->quotation?->sample_storage_condition }}</td>
        </tr>
    </tbody>
</table>

<!-- INVOICE ITEMS -->
<div class="section-title lg">INVOICED PARAMETERS<span class="rule"><span class="line"></span><span class="dmd"></span><span class="line"></span></span></div>
<table class="cell-table inv-head bordered">
    <thead>
        <tr class="thead">
            <th style="width:3%">S.N</th>
            <th style="width:6%">NABL/<br>NON NABL</th>
            <th style="width:13%">Discipline/<br>Group</th>
            <th style="width:12%">Materials or Products<br>(tested)</th>
            <th style="width:15%">Parameter</th>
            <th style="width:15%">Method</th>
            <th style="width:9%">Sample Qty<br>per sample (g/mL)</th>
            <th style="width:8%">No. of<br>Samples</th>
            <th style="width:10%">Charges<br>per sample</th>
            <th style="width:9%">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inv->items as $index => $item)
        @php
            $lt = $item->labTest;
        @endphp
        <tr class="inv-row">
            <td class="sn">{{ $index + 1 }}</td>
            <td>{{ $lt?->nabl_type }}</td>
            <td>{{ $lt?->discipline }}</td>
            <td>{{ $lt?->material }}</td>
            <td>{{ $lt?->parameter ?? $item->service_name_snapshot }}</td>
            <td>{{ $lt?->method }}</td>
            <td class="qty">{{ $lt?->sample_quantity }}</td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="charges">{{ $currencyFormat($item->unit_price_snapshot) }}</td>
            <td class="charges">{{ $currencyFormat($item->total) }}</td>
        </tr>
        @empty
        <tr class="inv-row">
            <td colspan="10" class="center">No parameters</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Totals -->
<table class="totals" style="margin-top:2pt">
    <tr>
        <td class="lbl">Taxable Amount</td>
        <td class="amnt">{{ $currencyFormat($inv->subtotal) }}</td>
    </tr>
    @if((float) $inv->discount > 0)
    <tr>
        <td class="lbl">Discount</td>
        <td class="amnt">{{ $currencyFormat($inv->discount) }}</td>
    </tr>
    @endif
    <tr>
        <td class="lbl">GST @ {{ $taxPct }}% (HSN Code: 998346)</td>
        <td class="amnt">{{ $currencyFormat($inv->tax) }}</td>
    </tr>
    <tr class="grand">
        <td>TOTAL AMOUNT PAYABLE</td>
        <td class="right">{{ $currencyFormat($inv->grand_total) }}</td>
    </tr>
</table>

<table class="amount-words" style="margin-top:2pt">
    <tr>
        <td class="k">Amount in Words</td>
        <td>{{ $amountInWords }} only</td>
    </tr>
</table>

<!-- ================= PAGE 2 ================= -->
<div class="page2">
    <div class="h2">BANK DETAILS<span class="rule"><span class="line"></span><span class="dmd"></span><span class="line"></span></span></div>
    <table class="bank">
        <tr>
            <td class="k">GSTIN</td>
            <td class="v">{{ $company['gst_number'] ?? '' }}</td>
            <td class="k">A/C Name</td>
            <td class="v">{{ $payment['account_name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="k">PAN</td>
            <td class="v">{{ $company['pan'] ?? $company['gst_number'] ?? '' }}</td>
            <td class="k">A/C Number</td>
            <td class="v">{{ $payment['account_number'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="k">Legal Name</td>
            <td class="v">{{ $company['legal_name'] ?? '' }}</td>
            <td class="k">Bank Name</td>
            <td class="v">{{ $payment['bank_name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="k">Trade Name</td>
            <td class="v">{{ $company['trade_name'] ?? '' }}</td>
            <td class="k">IFSC Code</td>
            <td class="v">{{ $payment['ifsc'] ?? '' }}</td>
        </tr>
    </table>

    <div class="terms-title">PAYMENT TERMS &amp; CONDITIONS<span class="rule"><span class="line"></span><span class="dmd"></span><span class="line"></span></span></div>

    <div class="terms-row"><span class="num">1.</span> Payment must be made 100% in advance via NEFT, RTGS, or any online payment mode. Cash or cheque payments are not accepted.</div>
    <div class="terms-row"><span class="num">2.</span> The analysis will commence only after receipt of full payment. The lead time will begin only after the full advance payment has been received.</div>
    <div class="terms-row"><span class="num">3.</span> The estimated timeframe for Test Report delivery is 15 working days under normal conditions. In case of any unforeseen circumstances, the delay will be communicated accordingly.</div>
    <div class="terms-row"><span class="num">4.</span> This invoice reflects only the parameters actually executed as per the approved quotation. Parameters not executed are not billed.</div>
    <div class="terms-row"><span class="num">5.</span> Once the Test Report is generated, no corrections or removal of any parameters will be entertained. Any retesting or reconfirmation of parameters will incur additional charges, subject to the availability of the sample.</div>
    <div class="terms-row"><span class="num">6.</span> The information provided by you will be kept confidential and not shared with anybody.</div>
    <div class="terms-row"><span class="num">7.</span> The sample to be delivered at: {{ $companyName }}, {{ $companyAddress }} Mob No - {{ $companyPhone }}.</div>
    <div class="terms-row"><span class="num">8.</span> By submitting samples for testing, the customer agrees to comply with the above Terms &amp; Conditions.</div>
</div>

</body>
</html>