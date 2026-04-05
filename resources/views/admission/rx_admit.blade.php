@extends('adminlte::page')

@section('title', 'Admission Prescription')

@section('content_header')
  <h1>Admission Prescription</h1>
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
  .rx-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:1rem;
    border-bottom:1px solid #e5e7eb;
    padding-bottom:.75rem;
  }
  .rx-title{
    font-size:1.4rem;
    font-weight:600;
  }
  .rx-meta{
    font-size:.9rem;
    color:#4b5563;
  }
  .rx-section-title{
    font-weight:600;
    margin-top:1rem;
    border-bottom:1px dashed #d1d5db;
    padding-bottom:.25rem;
    margin-bottom:.5rem;
  }
  .rx-box{
    border:1px solid #d1d5db;
    border-radius:.5rem;
    padding:.75rem;
    min-height:80px;
  }
  @media print{
    .no-print{display:none !important;}
    body{background:#fff;}
  }
</style>

<div class="no-print mb-3">
  <a href="{{ route('admission.list') }}" class="btn btn-secondary btn-sm">
    Back to Admission List
  </a>
  <button onclick="window.print()" class="btn btn-primary btn-sm">
    Print
  </button>
</div>

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
      <div><b>Admission Prescription</b></div>
      <div>Admission ID: {{ $admission->id }}</div>
      <div>Admit Date: {{ \Carbon\Carbon::parse($admission->admit_date)->format('d-m-Y') }}</div>
    </div>
  </div>

  <div>
    <div class="rx-section-title">Patient Information</div>
    <table class="table table-sm mb-0">
      <tr>
        <th style="width:22%">Name</th>
        <td>{{ $admission->patientname }}</td>
      </tr>
      <tr>
        <th>Mobile</th>
        <td>{{ $admission->mobile_no ?? $admission->mobileno }}</td>
      </tr>
      <tr>
        <th>Age / Gender</th>
        <td>{{ $admission->age ?? '-' }} / {{ $admission->gender ?? '-' }}</td>
      </tr>
      <tr>
        <th>Address</th>
        <td>{{ $admission->address ?? '-' }}</td>
      </tr>
    </table>
  </div>

  <div>
    <div class="rx-section-title">Reason for Admission / Provisional Diagnosis</div>
    <div class="rx-box">
      {{ $admission->reason ?? '______________________________________________' }}
    </div>
  </div>

  <div class="mt-3">
    <div class="rx-section-title">Initial Advice / Orders</div>
    <div class="rx-box" style="height:150px;">
      {{-- Doctor will write by hand after printing or you can type here later --}}
      <br>
      1.<br>
      2.<br>
      3.<br>
    </div>
  </div>

  <div class="mt-3">
    <div class="rx-section-title">Medicines (If any on Admission)</div>
    <div class="rx-box" style="height:150px;">
      Rx:<br><br>
      1.<br>
      2.<br>
      3.<br>
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
