@extends('adminlte::page')

@section('title', 'Add Investigation')

@section('content_header')
  <h1 class="text-primary">Add Investigation</h1>
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

    <form method="post" action="{{ route('settings.investigation.store') }}">
      @csrf

      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name') }}" required>
      </div>

      <div class="form-group">
        <label>Category *</label>
        <input type="text" name="category" class="form-control"
               value="{{ old('category') }}" required>
      </div>

      <div class="form-group">
        <label>Description *</label>
        <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
      </div>

      <div class="form-group">
        <label>Active *</label>
        <select name="active" class="form-control" required>
          <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>Active</option>
          <option value="0" {{ old('active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      <button class="btn btn-primary">Save</button>
      <a href="{{ route('settings.investigation.index') }}" class="btn btn-secondary">Back</a>

    </form>

  </div>
</div>

@stop
