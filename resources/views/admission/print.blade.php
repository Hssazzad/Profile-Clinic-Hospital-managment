{{-- resources/views/admission/print.blade.php --}}
@extends('adminlte::page')

@section('title', 'Admission Slip')

@section('content_header')
  <h1>Admission Slip</h1>
@stop

@section('content')

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
  .print-wrap{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:.75rem;
    padding:1.5rem;
    max-width:800px;
    margin:0 auto;
    font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  }
  .print-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:1rem;
    border-bottom:1px solid #e5e7eb;
    padding-bottom:.75rem;
  }
  .print-title{
    font-size:1.3rem;
    font-weight:600;
  }
  .print-meta{
    font-size:.9rem;
    color:#4b5563;
  }
  .print-section-title{
    font-weight:600;
    margin-top:1rem;
    border-bottom:1px dashed #d1d5db;
    padding-bottom:.25rem;
    margin-bottom:.5rem;
  }
  @media print {
    .no-print{display:none !important;}
    body{background:#fff;}
  }
</style>

<div class="no-print mb-3">
  <a href="{{ route('admission.admitpatient') }}" class="btn btn-secondary">
    New Admission
  </a>
  <button onclick="window.print()" class="btn btn-primary">
    Print
  </button>
</div>

<div class="print-wrap">
  <div class="print-header">
    <div>
      <div class="print-title">Hospital / Clinic Name</div>
      <div class="print-meta">
        Address line 1<br>
        Phone: 01XXXXXXXXX
      </div>
    </div>
    <div class="text-right">
      <div><b>Admission Slip</b></div>
      <div>Slip No: {{ $admission->id }}</div>
      <div>Date: {{ \Carbon\Carbon::parse($admission->admit_date)->format('d-m-Y') }}</div>
    </div>
  </div>

  <div>
    <div class="print-section-title">Patient Information</div>
    <table class="table table-sm mb-0">
      <tr>
        <th style="width:25%">Name</th>
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
    <div class="print-section-title">Admission Details</div>
    <table class="table table-sm mb-0">
      <tr>
        <th style="width:25%">Admit Date</th>
        <td>{{ \Carbon\Carbon::parse($admission->admit_date)->format('d-m-Y') }}</td>
      </tr>
      <tr>
        <th>Ward</th>
        <td>{{ $admission->ward ?? '-' }}</td>
      </tr>
      <tr>
        <th>Bed No.</th>
        <td>{{ $admission->bed_no ?? '-' }}</td>
      </tr>
      <tr>
        <th>Reason</th>
        <td>{{ $admission->reason ?? '-' }}</td>
      </tr>
    </table>
  </div>

  <div style="margin-top:2rem;display:flex;justify-content:space-between;">
    <div>
      ........................................... <br>
      Patient / Guardian Signature
    </div>
    <div>
      ........................................... <br>
      Authorized Signature
    </div>
  </div>

</div>
@endsection
