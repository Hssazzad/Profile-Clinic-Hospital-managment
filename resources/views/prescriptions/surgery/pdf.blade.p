{{-- resources/views/prescriptions/surgery/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surgery Prescription</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 30px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header h3 {
            margin: 5px 0;
            color: #666;
            font-weight: normal;
        }
        .prescription-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
        }
        .patient-info {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background: #333;
            color: white;
            padding: 8px;
            text-align: left;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 30px;
            border-top: 1px solid #333;
            width: 250px;
            margin-left: auto;
            padding-top: 10px;
        }
        .badge {
            padding: 3px 7px;
            border-radius: 3px;
            font-size: 11px;
        }
        .badge-pre-op { background: #ffc107; color: #333; }
        .badge-post-op { background: #28a745; color: white; }
        .badge-admission { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CITY HOSPITAL</h1>
        <h3>{{ $hospital_name ?? 'City Hospital' }}</h3>
    </div>

    <div class="prescription-info">
        <div class="info-row">
            <span class="info-label">Prescription #:</span> {{ $prescription->prescription_no ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span> {{ date('d-M-Y', strtotime($prescription->created_at)) }}
        </div>
        <div class="info-row">
            <span class="info-label">Doctor:</span> Dr. {{ $prescription->doctor_name ?? 'N/A' }}
        </div>
    </div>

    <div class="patient-info">
        <h3>PATIENT INFORMATION</h3>
        <div class="info-row">
            <span class="info-label">Name:</span> {{ $prescription->patient_name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="info-label">Patient ID:</span> {{ $prescription->patient_code ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="info-label">Age/Sex:</span> {{ $prescription->patient_age ?? 'N/A' }} Years / {{ $prescription->patient_gender ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span> {{ $prescription->patient_phone ?? 'N/A' }}
        </div>
    </div>

    <div style="background: #e8f4f8; padding: 15px; margin: 20px 0;">
        <h3>SURGERY DETAILS</h3>
        <div class="info-row">
            <span class="info-label">Type:</span> {{ $prescription->surgery_name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span> {{ date('d-M-Y', strtotime($prescription->surgery_date)) }}
        </div>
        <div class="info-row">
            <span class="info-label">Surgeon:</span> {{ $prescription->surgeon_name ?? 'N/A' }}
        </div>
    </div>

    <h3>PRESCRIBED MEDICINES</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Strength</th>
                <th>Dosage</th>
                <th>Duration</th>
                <th>Order Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicines as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->medicine_name }}</td>
                <td>{{ $item->strength ?? 'N/A' }}</td>
                <td>{{ $item->dose ?? 'N/A' }}</td>
                <td>{{ $item->duration ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-{{ str_replace('_', '-', $item->order_type) }}">
                        {{ ucfirst($item->order_type) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No medicines found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($prescription->instructions)
    <div style="margin-top: 30px;">
        <h3>INSTRUCTIONS</h3>
        <p>{{ $prescription->instructions }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature">
            <p>Dr. {{ $prescription->doctor_name ?? 'N/A' }}<br>
            Consultant Surgeon</p>
        </div>
    </div>

    <div style="margin-top: 30px; font-size: 10px; color: #999; text-align: center;">
        <p>Generated on: {{ $generated_at ?? date('d-M-Y h:i A') }}</p>
    </div>
</body>
</html>