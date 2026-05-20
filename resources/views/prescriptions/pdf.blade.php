{{-- resources/views/prescriptions/pdf.blade.php --}}
@php
    $pid = $pid ?? $rx->id ?? request('id');
@endphp
@php
    $doctorNames = '—';
    $doctorSpeciality = '';

    if (!empty($rxDoctors) && collect($rxDoctors)->count() > 0) {
        $doctorNames = collect($rxDoctors)->map(function($d){
            $name = $d->doctor_name ?? $d->doctor_name ?? '';
            $role = $d->role ?? '';
            return trim($name . ($role ? " ({$role})" : ''));
        })->filter()->values()->join(', ');

        $doctorSpeciality = collect($rxDoctors)
            ->pluck('speciality')
            ->filter()
            ->unique()
            ->values()
            ->join(', ');
    }
@endphp
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style>
  body {
    font-family: 'Helvetica', 'Arial', sans-serif;
    font-size: 11px;
    margin: 0;
    padding: 0;
    color: #333;
    background-color: #fff;
  }
  .header-section {
    background-color: #e3f2fd; /* Light Blue Tint */
    padding: 20px 30px;
    border-bottom: 2px solid #ddd;
  }
  .clinic-name {
    color: #006400; /* Dark Green */
    font-size: 24px;
    font-weight: bold;
  }
  .doctor-name {
    color: #d32f2f; /* Red */
    font-size: 18px;
    font-weight: bold;
  }
  .patient-info-bar {
    background-color: #fce4ec; /* Light Pink Bar */
    padding: 10px 30px;
  }
  .info-label {
    font-weight: bold;
    color: #555;
  }
  .info-value {
    border-bottom: 1px dotted #000;
    display: inline-block;
    min-width: 150px;
    margin-right: 20px;
  }

  /* Main Table Structure */
  .main-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  /* Left Side - Sidebar */
  .sidebar {
    width: 30%;
    background-color: #f0f7fa; /* Light Blue/Grey */
    padding: 20px 15px 30px 30px;
    border-right: 1px solid #d1d1d1;
    vertical-align: top;
  }

  /* Right Side - Content Area */
  .content-area {
    width: 70%;
    background-color: #ffffff; /* White */
    padding: 20px 30px 30px 20px;
    vertical-align: top;
    position: relative;
  }

  .section-title {
    font-weight: bold;
    font-size: 13px;
    text-decoration: underline;
    margin-bottom: 10px;
    color: #2c3e50;
  }

  .rx-symbol {
    font-size: 26px;
    color: #1a73e8;
    font-weight: bold;
    margin-bottom: 15px;
  }

  ul {
    list-style-type: none;
    padding-left: 0;
  }
  ul li {
    margin-bottom: 6px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 2px;
  }

  .footer {
    width: 100%;
    text-align: center;
    background-color: #f8f9fa;
    padding: 20px 0;
    font-size: 10px;
    color: #666;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
  }

  .clear { clear: both; }
</style>

<body>

<div class="header-section">
  <div style="float:left; width:60%;">
    <div class="clinic-name">Professor Clinic</div>
    <div style="font-size: 10px; color: #004d40;">Shah Amanat (R.) Road, G.E.C. Circle, Chattagram</div>
    <div style="font-size: 10px;">Phone: 880-18-19-433633</div>
  </div>
  <div style="float:right; width:40%; text-align:right;">
  <div class="doctor-name">{{ $doctorNames }}</div>

  @if($doctorSpeciality)
    <div style="font-size: 10px;">{{ $doctorSpeciality }}</div>
  @endif

  {{-- Optional fallback for old template --}}
  @if($doctorNames === '—')
    <div style="font-size: 10px;">MBBS, DCH, MD (Pediatrics)</div>
    <div style="font-size: 10px;">Professor & Head, Dept. of Pediatrics</div>
  @endif
</div>
  <div class="clear"></div>
</div>

<div class="patient-info-bar">
    <span class="info-label">Name:</span> <span class="info-value">{{ $patient->patientname ?? '________________' }}</span>
    <span class="info-label">Age:</span> <span class="info-value" style="min-width: 50px;">{{ $patient->age ?? '____' }}</span>
    <span class="info-label">Date:</span> <span class="info-value">{{ $rx->prescribed_on ?? now()->toDateString() }}</span>
</div>

<table class="main-table">
  <tr>
    <td class="sidebar">
      <div class="section-title">O/E</div>
      <ul style="font-size: 10px;">
          <li>Pulse:</li>
          <li>BP:</li>
          <li>Anaemia:</li>
          <li>Jaundice:</li>
          <li>Weight:</li>
          <li>Heart:</li>
          <li>Lungs:</li>
      </ul>

      <div class="section-title" style="margin-top:30px;">Investigations</div>
      <ul style="font-size: 9px; color: #444;">
          <li>• CBC/Hb%</li>
          <li>• Urine R/M/E</li>
          <li>• RBS/FBS</li>
          <li>• Blood grouping</li>
          <li>• S. creatinine</li>
          <li>• USG of ________</li>
          <li>• X-ray of ________</li>
      </ul>
    </td>

    <td class="content-area">
      <div class="rx-symbol">℞</div>
      
      @if(($medicines ?? collect())->count())
          @foreach($medicines as $m)
          <div style="margin-bottom: 18px; border-left: 3px solid #e3f2fd; padding-left: 10px;">
              <div style="font-weight: bold; font-size: 12px;">{{ $m->name }} {{ $m->strength ?? '' }}</div>
              <div style="margin-left: 5px; font-style: italic; color: #555;">
                  {{ $m->dose ?? '' }} — {{ $m->frequency ?? '' }} — {{ $m->duration ?? '' }}
              </div>
          </div>
          @endforeach
      @endif

      @if(!empty($rx?->advice))
      <div style="margin-top: 40px;">
          <div class="section-title">Advice</div>
          <div style="white-space: pre-line; color: #444;">{{ $rx->advice }}</div>
      </div>
      @endif
    </td>
  </tr>
</table>

<div class="footer">
    Please bring this prescription with you on your next visit after 
    <span style="border-bottom: 1px solid #666; padding: 0 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> days/months. <br>
    <strong>Professor Clinic</strong>
</div>

</body>