<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surgery Template - {{ $surgeryTemplate->template_name }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            
            body {
                font-family: 'Times New Roman', serif;
                font-size: 12px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .avoid-break {
                page-break-inside: avoid;
            }
        }
        
        @media screen {
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 20px;
            }
            
            .print-container {
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                max-width: 210mm;
                margin: 0 auto;
            }
            
            .print-only {
                display: none;
            }
        }
        
        /* Common styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .template-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            color: #333;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .medicine-item, .order-item, .investigation-item, .advice-item {
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
        }
        
        .medicine-item:before, .order-item:before, .investigation-item:before, .advice-item:before {
            content: "•";
            position: absolute;
            left: 0;
        }
        
        .notes-section {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 30px;
        }
        
        .signature-label {
            font-size: 10px;
        }
        
        /* Baby Note Section for C-Section */
        .baby-note {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin: 20px 0;
        }
        
        .baby-note-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="clinic-name">PROFESSOR CLINIC</div>
            <div>Surgery Template</div>
            <div class="template-title">{{ $surgeryTemplate->template_name }}</div>
        </div>

        <!-- Patient Information Section -->
        <div class="section avoid-break">
            <div class="section-title">Patient Information</div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 5px;">Name: _________________________</td>
                    <td style="width: 50%; padding: 5px;">Age: _______ Sex: _______</td>
                </tr>
                <tr>
                    <td style="padding: 5px;">Patient ID: ___________________</td>
                    <td style="padding: 5px;">Date: _________________________</td>
                </tr>
            </table>
        </div>

        <!-- Rx On Admission -->
        @if ($surgeryTemplate->rx_admission && count($surgeryTemplate->rx_admission) > 0)
        <div class="section avoid-break">
            <div class="section-title">Rx On Admission</div>
            @foreach ($surgeryTemplate->rx_admission as $medicine)
                <div class="medicine-item">
                    {{ $medicine['type'] }} - {{ $medicine['company_name'] }}
                    @if (isset($medicine['dosage']) && $medicine['dosage'])
                        ({{ $medicine['dosage'] }})
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Pre-Operative Orders -->
        @if ($surgeryTemplate->pre_op_orders && count($surgeryTemplate->pre_op_orders) > 0)
        <div class="section avoid-break">
            <div class="section-title">Pre-Operative Orders</div>
            @foreach ($surgeryTemplate->pre_op_orders as $order)
                <div class="order-item">{{ $order }}</div>
            @endforeach
        </div>
        @endif

        <!-- Post-Operative Orders -->
        @if ($surgeryTemplate->post_op_orders && count($surgeryTemplate->post_op_orders) > 0)
        <div class="section avoid-break">
            <div class="section-title">Post-Operative Orders</div>
            @foreach ($surgeryTemplate->post_op_orders as $order)
                <div class="order-item">{{ $order }}</div>
            @endforeach
        </div>
        @endif

        <!-- Baby Note (for C-Section) -->
        @if (str_contains(strtolower($surgeryTemplate->template_name), 'c-section') || str_contains(strtolower($surgeryTemplate->template_name), 'caesarean'))
        <div class="baby-note avoid-break">
            <div class="baby-note-title">Baby Note</div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px;">Sex: _______ Weight: _______ kg</td>
                    <td style="padding: 5px;">APGAR Score: 1 min: _______ 5 min: _______</td>
                </tr>
                <tr>
                    <td style="padding: 5px;" colspan="2">Condition: ___________________________________________________</td>
                </tr>
                <tr>
                    <td style="padding: 5px;" colspan="2">Feeding: _________________________________________________________</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Investigations -->
        @if ($surgeryTemplate->investigations && count($surgeryTemplate->investigations) > 0)
        <div class="section avoid-break">
            <div class="section-title">Investigations</div>
            @foreach ($surgeryTemplate->investigations as $investigation)
                <div class="investigation-item">{{ $investigation }}</div>
            @endforeach
        </div>
        @endif

        <!-- Advices -->
        @if ($surgeryTemplate->advices && count($surgeryTemplate->advices) > 0)
        <div class="section avoid-break">
            <div class="section-title">General Advices</div>
            @foreach ($surgeryTemplate->advices as $advice)
                <div class="advice-item">{{ $advice }}</div>
            @endforeach
        </div>
        @endif

        <!-- Notes -->
        @if ($surgeryTemplate->notes)
        <div class="notes-section avoid-break">
            <div class="section-title">Additional Notes</div>
            <div>{{ $surgeryTemplate->notes }}</div>
        </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="section avoid-break">
            <div class="section-title">Terms & Conditions</div>
            <div style="font-size: 10px; line-height: 1.3;">
                <p>1. This prescription is based on the clinical examination and medical history provided.</p>
                <p>2. Please follow the medication schedule strictly as prescribed.</p>
                <p>3. Report immediately if any adverse reactions occur.</p>
                <p>4. Keep all medicines out of reach of children.</p>
                <p>5. This prescription is valid for the specified treatment period only.</p>
                <p>6. Follow-up appointments are essential for monitoring progress.</p>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section avoid-break">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Doctor's Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Date & Time</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Generated on: {{ now()->format('d-M-Y H:i') }} | Template ID: {{ $surgeryTemplate->id }}</div>
            <div>© Professor Clinic - Professional Healthcare Management System</div>
        </div>
    </div>

    <!-- Print Button (only visible on screen) -->
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Template
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
</body>
</html>
