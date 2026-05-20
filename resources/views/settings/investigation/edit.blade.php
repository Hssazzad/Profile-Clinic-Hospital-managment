@extends('adminlte::page')

@section('title', 'Edit Investigation')

@section('content_header')
  <h1 class="text-primary">Edit Investigation</h1>
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

    <form method="post" action="{{ route('settings.investigation.update', $inv->id) }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Name *</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $inv->name) }}" required>
      </div>

      <div class="form-group">
        <label>Category *</label>
        <input type="text" name="category" class="form-control"
               value="{{ old('category', $inv->category) }}" required>
      </div>

      <div class="form-group">
        <label>Description *</label>
        <textarea name="description" class="form-control" rows="3" required>{{ old('description', $inv->description) }}</textarea>
      </div>

      <div class="form-group">
        <label>Active *</label>
        <select name="active" class="form-control" required>
          <option value="1" {{ $inv->active ? 'selected' : '' }}>Active</option>
          <option value="0" {{ !$inv->active ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      <button class="btn btn-primary">Update</button>
      <a href="{{ route('settings.investigation.index') }}" class="btn btn-secondary">Back</a>

    </form>

  </div>
</div>

@stop
