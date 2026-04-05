@extends('adminlte::page')

@section('title', 'Discharge Prescription')

@section('content_header')
  <h1>Discharge Prescription</h1>
@stop

@section('content')

<style>
  .rx-wrap{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:.75rem;
    padding:1.5rem;
    max-width:900px;
    margin:0 auto;
    font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  }
  .rx-header{ /* same as before */ }
  .rx-section-title{font-weight:600;margin-top:1rem;border-bottom:1px dashed #d1d5db;padding-bottom:.25rem;margin-bottom:.5rem;}
  .rx-box{border:1px solid #d1d5db;border-radius:.5rem;padding:.75rem;min-height:80px;}
  @media print{.no-print{display:none !important;} body{background:#fff;}}
</style>

{{-- 🔹 Non-print controls --}}
<div class="no-print mb-3">
  <a href="{{ route('admission.list') }}" class="btn btn-secondary btn-sm">
    Back to Admission List
  </a>
  <button onclick="window.print()" class="btn btn-primary btn-sm">
    Print
  </button>
</div>

{{-- 🔹 Editable form for final data (screen only) --}}
<div class="no-print mb-3">
  <div class="card">
    <div class="card-header">
      <strong>Edit Final Diagnosis, Advice & Medicines</strong>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('admission.rx.discharge.save', $admission->id) }}">
        @csrf

        <div class="form-group">
          <label>Final Diagnosis</label>
          <textarea name="final_diagnosis" rows="3" class="form-control">{{ old('final_diagnosis', $admission->final_diagnosis ?? '') }}</textarea>
        </div>

        <div class="form-group">
          <label>Advice on Discharge</label>
          <textarea name="final_advice" rows="4" class="form-control">{{ old('final_advice', $admission->final_advice ?? '') }}</textarea>
        </div>

        <div class="form-group">
          <label>Final Medicines (one line per medicine)</label>
          <textarea name="final_medicines" rows="5" class="form-control">{{ old('final_medicines', $admission->final_medicines ?? '') }}</textarea>
          <small class="text-muted">Example: Tab. Napa 500mg – 1+0+1 – 5 days</small>
        </div>

        <button class="btn btn-primary btn-sm">Save Final Data</button>
      </form>
    </div>
  </div>
</div>

{{-- 🔹 Printable layout --}}
<div class="rx-wrap">
  <div class="rx-header">
    <div>
      <div class="rx-title">Hospital / Clinic Name</div>
      <div class="rx-meta">
        Address line 1<br>
        Phone: 01XXXXXXXXX
      </div>
    </div>
    <div class="text-right">
      <div><b>Discharge Prescription</b></div>
      <div>Admission ID: {{ $admission->id }}</div>
      <div>Admit: {{ \Carbon\Carbon::parse($admission->admit_date)->format('d-m-Y') }}</div>
      <div>Discharge:
        @if($admission->discharge_date)
          {{ \Carbon\Carbon::parse($admission->discharge_date)->format('d-m-Y') }}
        @else
          __________________
        @endif
      </div>
    </div>
  </div>

  {{-- Patient info --}}
  <div>
    <div class="rx-section-title">Patient Information</div>
    <table class="table table-sm mb-0">
      <tr><th style="width:22%">Name</th><td>{{ $admission->patientname }}</td></tr>
      <tr><th>Mobile</th><td>{{ $admission->mobile_no }}</td></tr>
      <tr><th>Age / Gender</th><td>{{ $admission->age ?? '-' }} / {{ $admission->gender ?? '-' }}</td></tr>
      <tr><th>Address</th><td>{{ $admission->address ?? '-' }}</td></tr>
    </table>
  </div>

  {{-- Final Diagnosis (printed from saved data) --}}
  <div>
    <div class="rx-section-title">Final Diagnosis</div>
    <div class="rx-box">
      @if(!empty($admission->final_diagnosis))
        {!! nl2br(e($admission->final_diagnosis)) !!}
      @else
        ________________________________________<br>
        ________________________________________<br>
      @endif
    </div>
  </div>

  {{-- Advice on Discharge --}}
  <div class="mt-3">
    <div class="rx-section-title">Advice on Discharge</div>
    <div class="rx-box">
      @if(!empty($admission->final_advice))
        {!! nl2br(e($admission->final_advice)) !!}
      @else
        1.<br>
        2.<br>
        3.<br>
      @endif
    </div>
  </div>

  {{-- Final Medicines --}}
  <div class="mt-3">
    <div class="rx-section-title">Medicines After Discharge</div>
    <div class="rx-box">
      @if(!empty($admission->final_medicines))
        {!! nl2br(e($admission->final_medicines)) !!}
      @else
        Rx:<br><br>
        1.<br>
        2.<br>
        3.<br>
        4.<br>
      @endif
    </div>
  </div>

  <div style="margin-top:2rem;display:flex;justify-content:space-between;">
    <div>
      ........................................... <br>
      Patient / Guardian Signature
    </div>
    <div>
      ........................................... <br>
      Doctor's Signature
    </div>
  </div>
</div>

@endsection
