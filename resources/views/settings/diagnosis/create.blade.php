@extends('adminlte::page')

@section('title', 'Add Diagnosis')

@section('content_header')
  <h1 class="text-primary">Add Diagnosis</h1>
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

    <form method="post" action="{{ route('settings.diagnosis.store') }}">
      @csrf

      <div class="form-group">
        <label>Code *</label>
        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
      </div>

      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      </div>

      <div class="form-group">
        <label>Active *</label>
        <select name="active" class="form-control" required>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <button class="btn btn-primary">Save</button>
      <a href="{{ route('settings.diagnosis.index') }}" class="btn btn-secondary">
        Back
      </a>

    </form>

  </div>
</div>

@stop
