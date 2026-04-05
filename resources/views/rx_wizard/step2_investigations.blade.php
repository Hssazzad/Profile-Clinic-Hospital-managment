@extends('adminlte::page')
@section('title','New Prescription — Step 2')
@section('content_header') <h1>Step 2/5 — Investigations</h1> @stop
@section('content')
<form method="post" action="{{ route('rx.post2') }}" class="card card-body">
  @csrf
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <label for="investigation">Select Investigation(s)</label>
  <select id="investigation" name="investigation[]" class="form-control" multiple size="10" required>
    @foreach($investigations as $i)
      <option value="{{ $i->name }}">{{ $i->name }}</option>
    @endforeach
  </select>
  <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple.</small>

  <label for="note_all" class="mt-3">Notes (optional, applies to all)</label>
  <input type="text" id="note_all" name="note_all" class="form-control" placeholder="e.g., fasting">

  <div class="mt-3 d-flex">
    <a href="{{ route('rx.step1') }}" class="btn btn-light mr-2">? Back</a>
    <button class="btn btn-primary">Next: Diagnosis</button>
  </div>
</form>
@endsection
