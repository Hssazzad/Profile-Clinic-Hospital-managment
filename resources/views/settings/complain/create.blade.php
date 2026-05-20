@extends('adminlte::page')

@section('title', 'Add Complain')

@section('content_header')
  <h1 class="text-primary">Add Common Complain</h1>
@stop

@section('content')

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-body">

    <form method="post" action="{{ route('settings.complain.store') }}">
      @csrf

      <div class="form-group">
        <label>Code (optional)</label>
        <input type="number" name="code" class="form-control"
               value="{{ old('code') }}">
      </div>

      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name') }}" required maxlength="100">
      </div>

      <div class="form-group">
        <label>Active *</label>
        <select name="active" class="form-control" required>
          <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>Active</option>
          <option value="0" {{ old('active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      <button class="btn btn-primary">Save</button>
      <a href="{{ route('settings.complain.index') }}" class="btn btn-secondary">
        Back
      </a>

    </form>

  </div>
</div>

@stop
