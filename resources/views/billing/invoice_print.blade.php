<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice  {{ $payment->BillNo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #f4f4f4;
        }

        .invoice-wrapper {
            max-width: 760px;
            margin: 30px auto;
            background: #fff;
            padding: 36px 40px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
        }

        .invoice-header {
            text-align: center;
            border-bottom: 3px double #1a3a5c;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .clinic-sub {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .invoice-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 0.5px;
        }
        .invoice-meta {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
            line-height: 1.7;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid    { background: #28a745; }
        .status-partial { background: #e6a817; }
        .status-due     { background: #dc3545; }

        .patient-box {
            background: #f5f8ff;
            border: 1px solid #c8d8f0;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px 20px;
        }
        .patient-grid .field label {
            display: block;
            font-size: 11px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .patient-grid .field span {
            font-weight: bold;
            font-size: 13px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 12.5px;
        }
        .items-table thead tr {
            background: #1a3a5c;
            color: #fff;
        }
        .items-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .items-table thead th.text-right  { text-align: right; }
        .items-table thead th.text-center { text-align: center; }
        .items-table tbody tr:nth-child(even) { background: #f9f9f9; }
        .items-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .items-table tbody td.text-right  { text-align: right; }
        .items-table tbody td.text-center { text-align: center; }

        .print-inv-badge {
            display: inline-block;
            background: #e0f2f1;
            color: #00695c;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 10px;
            white-space: nowrap;
            letter-spacing: .2px;
        }

        .print-doc-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #c62828;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 10px;
            white-space: nowrap;
            letter-spacing: .2px;
        }

        .no-items td {
            padding: 16px;
            text-align: center;
            color: #999;
            font-style: italic;
        }

        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .totals-table {
            min-width: 280px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .totals-table tr td {
            padding: 6px 14px;
            font-size: 13px;
        }
        .totals-table tr:nth-child(even) { background: #f9f9f9; }
        .totals-table .total-row {
            background: #1a3a5c !important;
            color: #fff;
            font-size: 15px;
            font-weight: bold;
        }
        .totals-table .paid-row { color: #28a745; font-weight: bold; }
        .totals-table .due-row  { color: #dc3545; font-weight: bold; background: #fff8f8 !important; }
        .text-right { text-align: right; }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #ccc;
        }
        .sig-line {
            border-top: 1px solid #555;
            padding-top: 5px;
            margin-top: 36px;
            text-align: center;
            font-size: 12px;
            color: #444;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 18px;
            font-size: 10.5px;
            color: #aaa;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }

        .print-actions {
            text-align: center;
            margin: 20px 0 10px;
        }
        .btn-print {
            background: #1a3a5c;
            color: #fff;
            border: none;
            padding: 10px 36px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 8px;
        }
        .btn-print:hover { background: #254d78; }
        .btn-back {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back:hover { background: #5a6268; color: #fff; text-decoration: none; }

        @media print {
            body { background: #fff; }
            .invoice-wrapper {
                margin: 0;
                padding: 20px 24px;
                border: none;
                box-shadow: none;
                max-width: 100%;
            }
            .print-actions { display: none !important; }
            .print-inv-badge {
                border: 1px solid #00695c;
                background: #e0f2f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-doc-badge {
                border: 1px solid #c62828;
                background: #e3f2fd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button class="btn-print" onclick="window.print()">
        &#128438; Print Invoice
    </button>
    <a href="{{ route('billing.payment.index') }}" class="btn-back">
        &#8592; Back to Billing
    </a>
</div>

<div class="invoice-wrapper">

    {{-- HEADER --}}
    <div class="invoice-header">
        <div class="clinic-name">{{ config('app.name', 'Professor Clinic') }}</div>
        <div class="clinic-sub">Medical Services &mdash; Specialist &amp; General Care</div>
    </div>

    {{-- TITLE ROW --}}
    <div class="invoice-title-row">
        <div>
            <div class="invoice-title">PAYMENT INVOICE</div>
            <div class="invoice-meta">
                Bill No: <strong>{{ $payment->BillNo }}</strong><br>
                Date: <strong>{{ $payment->PaymentDate }}</strong><br>
                Method: <strong>{{ ucwords(str_replace('_', ' ', $payment->PaymentMethod)) }}</strong>
            </div>
        </div>
        <div style="text-align:right;">
            @php
                $statusClass = match(strtolower($payment->Status)) {
                    'paid'    => 'status-paid',
                    'partial' => 'status-partial',
                    default   => 'status-due',
                };
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ strtoupper($payment->Status) }}</span>
            @if($payment->CollectedBy)
            <div style="font-size:11px; color:#777; margin-top:6px;">
                Collected by: <strong>{{ $payment->CollectedBy }}</strong>
            </div>
            @endif
        </div>
    </div>

    {{-- PATIENT INFO --}}
    <div class="patient-box">
        <div class="patient-grid">
            <div class="field">
                <label>Patient Name</label>
                <span>{{ $payment->PatientName }}</span>
            </div>
            <div class="field">
                <label>Patient Code</label>
                <span>{{ $payment->PatientCode ?? '' }}</span>
            </div>
            <div class="field">
                <label>Age</label>
                <span>{{ $payment->PatientAge ?? '' }}</span>
            </div>
            <div class="field">
                <label>Mobile</label>
                <span>{{ $payment->MobileNo ?? '' }}</span>
            </div>
            <div class="field">
                <label>Admission ID</label>
                <span>{{ $payment->AdmissionId ? '#'.$payment->AdmissionId : '' }}</span>
            </div>
            <div class="field">
                <label>Invoice Date</label>
                <span>{{ $payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') : '' }}</span>
            </div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:18%">Category</th>
                <th>Service / Item</th>
                <th class="text-right" style="width:13%">Unit Price</th>
                <th class="text-center" style="width:7%">Qty</th>
                <th class="text-right" style="width:13%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @php
                        $isDoctor = ($item->CategoryName == 'Doctor Consultation');
                        $badgeClass = $isDoctor ? 'print-doc-badge' : 'print-inv-badge';
                    @endphp
                    <span class="{{ $badgeClass }}">
                        {{ $item->CategoryName ?? $item->CategoryCode ?? '' }}
                    </span>
                </td>
                <td>
                    {{ $item->ServiceName }}
                    @if($item->Remarks)
                        <div style="font-size: 11px; color: #777; margin-top: 2px;">{{ $item->Remarks }}</div>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->UnitPrice, 2) }}</td>
                <td class="text-center">{{ $item->Quantity }}</td>
                <td class="text-right"><strong>{{ number_format($item->Amount, 2) }}</strong></td>
            </tr>
            @empty
            <tr class="no-items">
                <td colspan="6"> General payment (no itemised breakdown) </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TOTALS --}}
    @php
        $net = (float)$payment->TotalBill - (float)($payment->Discount ?? 0);
    @endphp
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td>Gross Amount</td>
                <td class="text-right">{{ number_format($payment->TotalBill, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="text-right" style="color:#e74c3c;">
                    &minus; {{ number_format($payment->Discount ?? 0, 2) }}
                </td>
            </tr>
            <tr class="total-row">
                <td>Net Payable</td>
                <td class="text-right">{{ number_format($net, 2) }}</td>
            </tr>
            <tr class="paid-row">
                <td>Paid Amount</td>
                <td class="text-right">{{ number_format($payment->PaidAmount, 2) }}</td>
            </tr>
            <tr class="due-row">
                <td>Due Amount</td>
                <td class="text-right">{{ number_format($payment->DueAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- SIGNATURES --}}
    <div class="signatures">
        <div>
            <div class="sig-line">Patient / Guardian Signature</div>
        </div>
        <div style="text-align:right;">
            <div class="sig-line">Authorized Signature &amp; Seal</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="invoice-footer">
        This is a computer-generated invoice &mdash; {{ config('app.name', 'Professor Clinic') }}
        &nbsp;|&nbsp; Printed: {{ now()->format('d M Y, h:i A') }}
    </div>

</div>

</body>
</html>