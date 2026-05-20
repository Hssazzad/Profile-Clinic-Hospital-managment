@extends('adminlte::page')
@section('title','New Prescription — Step 4')
@section('content_header') <h1>Step 4/5 — Medicines</h1> @stop
@section('content')
<form method="post" action="{{ route('rx.post4') }}" class="card card-body">
  @csrf
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <label for="medicine_id">Select Medicine(s)</label>
  <select id="medicine_id" name="medicine_id[]" class="form-control" multiple size="12" required>
    @foreach($medicines as $m)
      <option value="{{ $m->id }}">{{ $m->name }}</option>
    @endforeach
  </select>
  <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple.</small>

  <div class="row mt-3">
    <div class="col-md-4 mb-2">
      <label for="strength">Strength</label>
      <input type="text" id="strength" name="strength" class="form-control" placeholder="e.g., 500 mg">
    </div>
    <div class="col-md-4 mb-2">
      <label for="dose">Dose</label>
      <input type="text" id="dose" name="dose" class="form-control" placeholder="e.g., 1 tab">
    </div>
    <div class="col-md-4 mb-2">
      <label for="route">Route</label>
      <input type="text" id="route" name="route" class="form-control" placeholder="e.g., oral">
    </div>
    <div class="col-md-4 mb-2">
      <label for="frequency">Frequency</label>
      <input type="text" id="frequency" name="frequency" class="form-control" placeholder="e.g., TID">
    </div>
    <div class="col-md-4 mb-2">
      <label for="duration">Duration</label>
      <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g., 5 days">
    </div>
    <div class="col-md-4 mb-2">
      <label for="timing">Timing</label>
      <input type="text" id="timing" name="timing" class="form-control" placeholder="e.g., after meal">
    </div>
  </div>

  <div class="mt-3 d-flex">
    <a href="{{ route('rx.step3') }}" class="btn btn-light mr-2">? Back</a>
    <button class="btn btn-primary">Next: Review</button>
  </div>
</form>
@endsection
