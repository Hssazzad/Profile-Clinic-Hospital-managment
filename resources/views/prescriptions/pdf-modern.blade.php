<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Prescription #{{ $prescription->prescription_no }}</title>
    <style>
        @font-face {
            font-family: 'NotoSansBengali';
            src: url('{{ public_path('fonts/NotoSansBengali-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #fff;
            line-height: 1.4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }

        .clinic-info {
            text-align: left;
        }

        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .clinic-address {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .doctor-info {
            text-align: right;
        }

        .doctor-name {
            font-size: 18px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 5px;
        }

        .doctor-credentials {
            font-size: 11px;
            color: #666;
        }

        .patient-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .patient-info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .patient-info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            min-width: 120px;
        }

        .info-value {
            color: #000;
            border-bottom: 1px dotted #999;
            min-width: 150px;
            display: inline-block;
        }

        .prescription-content {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .left-section {
            flex: 0 0 30%;
            background-color: #f0f7fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e1e5e9;
        }

        .right-section {
            flex: 0 0 70%;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            text-decoration: underline;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .rx-symbol {
            font-size: 32px;
            color: #1a73e8;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .medicine-item {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fff;
            border-left: 3px solid #007bff;
            border-radius: 3px;
        }

        .medicine-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .medicine-details {
            font-style: italic;
            color: #555;
            font-size: 11px;
        }

        .advice-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .qr-code {
            margin: 10px auto;
        }

        .verification-text {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .qr-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="clinic-info">
            <div class="clinic-name">Professional Clinic</div>
            <div class="clinic-address">123 Medical Center Road, Dhaka 1000</div>
            <div class="clinic-address">Phone: +880 1234-567890 | Email: info@professionalclinic.com</div>
        </div>
        <div class="doctor-info">
            <div class="doctor-name">{{ $prescription->doctor_name }}</div>
            <div class="doctor-credentials">Registration No: {{ $prescription->doctor_reg_no }}</div>
            <div class="doctor-credentials">Prescription No: {{ $prescription->prescription_no }}</div>
            <div class="doctor-credentials">Date: {{ $prescription->prescribed_on ? $prescription->prescribed_on->format('d M Y') : 'N/A' }}</div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="patient-info">
        <div class="patient-info-row">
            <span class="info-label">Patient Name:</span>
            <span class="info-value">{{ $prescription->patient->patientname }}</span>
            <span class="info-label" style="margin-left: 20px;">Patient Code:</span>
            <span class="info-value">{{ $prescription->patient->patientcode }}</span>
        </div>
        <div class="patient-info-row">
            <span class="info-label">Age:</span>
            <span class="info-value">{{ $prescription->patient->age ?? 'N/A' }} years</span>
            <span class="info-label" style="margin-left: 20px;">Gender:</span>
            <span class="info-value">{{ $prescription->patient->gender ?? 'N/A' }}</span>
        </div>
        <div class="patient-info-row">
            <span class="info-label">Mobile:</span>
            <span class="info-value">{{ $prescription->patient->mobile_no ?? 'N/A' }}</span>
            <span class="info-label" style="margin-left: 20px;">Blood Group:</span>
            <span class="info-value">{{ $prescription->patient->blood_group ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Diagnosis -->
    @if($prescription->diagnosis)
    <div style="margin-bottom: 20px;">
        <div class="section-title">Diagnosis</div>
        <div style="padding: 10px; background-color: #e8f5e8; border-radius: 5px; border-left: 4px solid #28a745;">
            {{ $prescription->diagnosis }}
        </div>
    </div>
    @endif

    <!-- Prescription Content -->
    <div class="prescription-content">
        <div class="left-section">
            <div class="section-title">Clinical Findings</div>
            <div style="font-size: 11px; margin-bottom: 15px;">
                <div>Pulse: _______ bpm</div>
                <div>BP: ______/_______ mmHg</div>
                <div>Temperature: ______°F</div>
                <div>Weight: _______ kg</div>
                <div>Heart: Normal</div>
                <div>Lungs: Clear</div>
            </div>

            <div class="section-title">Investigations</div>
            <div style="font-size: 11px;">
                <div>• CBC/Hb%</div>
                <div>• RBS/FBS</div>
                <div>• Urine R/M/E</div>
                <div>• Blood Grouping & Rh</div>
                <div>• Serum Creatinine</div>
                <div>• Lipid Profile</div>
            </div>
        </div>

        <div class="right-section">
            <div class="rx-symbol">℞</div>

            @if($prescription->items->count() > 0)
                @foreach($prescription->items as $item)
                    <div class="medicine-item">
                        <div class="medicine-name">{{ $item->medicine_name }}</div>
                        <div class="medicine-details">
                            @if($item->dose) {{ $item->dose }} @endif
                            @if($item->frequency) — {{ $item->frequency }} @endif
                            @if($item->duration) — {{ $item->duration }} @endif
                            @if($item->note) <br>{{ $item->note }} @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; color: #999; padding: 20px;">
                    No medications prescribed
                </div>
            @endif

            @if($prescription->advices)
            <div class="advice-section">
                <div class="section-title">Doctor's Advice</div>
                <div style="white-space: pre-line;">{{ $prescription->advices }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="qr-section">
        <div style="font-weight: bold; margin-bottom: 10px;">Prescription Verification</div>
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url('/prescriptions/' . $prescription->id) }}"
                 alt="QR Code" style="border: 1px solid #ddd; padding: 5px;">
        </div>
        <div class="verification-text">
            Scan to verify authenticity<br>
            Prescription ID: {{ $prescription->prescription_no }}
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div><strong>Professional Clinic</strong> - Your Health, Our Priority</div>
        <div>Please bring this prescription on your next visit</div>
        <div>Generated on: {{ now()->format('d M Y H:i:s') }}</div>
    </div>
</body>
</html>
