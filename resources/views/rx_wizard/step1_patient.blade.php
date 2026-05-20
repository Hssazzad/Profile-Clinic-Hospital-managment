@extends('adminlte::page')
@section('title','New Prescription — Step 1')
@section('content_header') <h1>Step 1/5 — Select Patient</h1> @stop
@section('content')
<form method="post" action="{{ route('rx.post1') }}" class="card card-body">
  @csrf
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <label for="patient_id">Patient</label>
  <select id="patient_id" name="patient_id" class="form-control" required>
    <option value="">-- Select patient --</option>
    @foreach($patients as $p)
      <option value="{{ $p->id }}" {{ $rx['patient_id']==$p->id?'selected':'' }}>
        {{ $p->patientname }}{{ $p->patientcode ? ' — '.$p->patientcode  : '' }}{{ $p->mobile_no ? ' — '.$p->mobile_no : '' }}
      </option>
    @endforeach
  </select>

  <div class="mt-3 d-flex">
    <button class="btn btn-primary">Next: Investigations</button>
    <form method="post" action="{{ route('rx.reset') }}" class="ml-2">@csrf<button class="btn btn-outline-secondary">Reset</button></form>
  </div>
</form>
@endsection
