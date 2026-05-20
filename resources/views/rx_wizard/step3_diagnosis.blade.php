@extends('adminlte::page')
@section('title','New Prescription — Step 3')
@section('content_header') <h1>Step 3/5 — Diagnosis</h1> @stop
@section('content')
<form method="post" action="{{ route('rx.post3') }}" class="card card-body">
  @csrf
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <label for="diagnosis">Select Diagnosis</label>
  <select id="diagnosis" name="diagnosis[]" class="form-control" multiple size="10" required>
    @foreach($diagnoses as $d)
      <option value="{{ $d->name }}">{{ $d->name }}</option>
    @endforeach
  </select>

  <div class="mt-3 d-flex">
    <a href="{{ route('rx.step2') }}" class="btn btn-light mr-2">? Back</a>
    <button class="btn btn-primary">Next: Medicines</button>
  </div>
</form>
@endsection
