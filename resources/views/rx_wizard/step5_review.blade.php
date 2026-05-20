@extends('adminlte::page')
@section('title','New Prescription — Review')
@section('content_header') <h1>Step 5/5 — Review & Save</h1> @stop
@section('content')
<div class="card card-body">
  <h5 class="mb-3">Patient</h5>
  <p class="mb-2"><strong>{{ $patient->name }}</strong>
    @if($patient->code) — {{ $patient->code }} @endif
    @if($patient->mobile) — {{ $patient->mobile }} @endif
  </p>

  <hr>
  <h5>Investigations</h5>
  <ul>
    @foreach($rx['investigations'] as $i)
      <li>{{ $i['name'] }} @if(!empty($i['note'])) <em>({{ $i['note'] }})</em> @endif</li>
    @endforeach
  </ul>

  <h5>Diagnosis</h5>
  <ul>
    @foreach($rx['diagnosis'] as $d)
      <li>{{ $d }}</li>
    @endforeach
  </ul>

  <h5>Medicines</h5>
  <ul>
    @foreach($rx['medicines'] as $m)
      <li>
        <strong>{{ $m['name'] }}</strong>
        @php $parts = array_filter([$m['strength'],$m['dose'],$m['route'],$m['frequency'],$m['duration'],$m['timing']]); @endphp
        @if(!empty($parts)) — {{ implode(', ', $parts) }} @endif
      </li>
    @endforeach
  </ul>

  <form method="post" action="{{ route('rx.finalize') }}" class="mt-3">
    @csrf
    @if ($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="prescribed_on">Prescribed On</label>
        <input type="date" id="prescribed_on" name="prescribed_on" class="form-control" value="{{ $rx['meta']['prescribed_on'] }}">
      </div>
      <div class="form-group col-md-4">
        <label for="doctor_name">Doctor Name</label>
        <input type="text" id="doctor_name" name="doctor_name" class="form-control" value="{{ $rx['meta']['doctor_name'] }}">
      </div>
      <div class="form-group col-md-4">
        <label for="doctor_reg_no">Doctor Reg. No</label>
        <input type="text" id="doctor_reg_no" name="doctor_reg_no" class="form-control" value="{{ $rx['meta']['doctor_reg_no'] }}">
      </div>
    </div>

    <div class="d-flex">
      <a href="{{ route('rx.step4') }}" class="btn btn-light mr-2">? Back</a>
      <button class="btn btn-success">Save Prescription</button>
    </div>
  </form>

  <form method="post" action="{{ route('rx.reset') }}" class="mt-2">
    @csrf
    <button class="btn btn-outline-secondary btn-sm">Reset Wizard</button>
  </form>
</div>
@endsection
