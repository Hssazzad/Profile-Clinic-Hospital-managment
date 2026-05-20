@extends('adminlte::page')

@section('title', 'Edit Diagnosis')

@section('content_header')
  <h1 class="text-primary">Edit Diagnosis</h1>
@stop

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="card">
  <div class="card-body">

    <form method="post" action="{{ route('settings.diagnosis.update', $d->id) }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Code *</label>
        <input type="text" name="code" class="form-control"
               value="{{ old('code', $d->code) }}" required>
      </div>

      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $d->name) }}" required>
      </div>

      <div class="form-group">
        <label>Active *</label>
        <select name="active" class="form-control" required>
          <option value="1" {{ $d->active ? 'selected' : '' }}>Active</option>
          <option value="0" {{ !$d->active ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      <button class="btn btn-primary">Update</button>
      <a href="{{ route('settings.diagnosis.index') }}" class="btn btn-secondary">
        Back
      </a>

    </form>

  </div>
</div>

@stop
