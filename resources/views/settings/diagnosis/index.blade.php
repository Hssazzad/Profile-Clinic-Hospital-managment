@extends('adminlte::page')

@section('title', 'Diagnosis List')

@section('content_header')
  <h1 class="text-primary">Diagnosis List</h1>
@stop

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('settings.diagnosis.create') }}" class="btn btn-primary mb-3">
  + Add Diagnosis
</a>

<div class="card">
  <div class="table-responsive p-0">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Name</th>
          <th>Active</th>
          <th width="120">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($diagnosis as $i => $d)
        <tr>
          <td>{{ $diagnosis->firstItem() + $i }}</td>
          <td>{{ $d->code }}</td>
          <td>{{ $d->name }}</td>
          <td>
            @if($d->active)
              <span class="badge badge-success">Active</span>
            @else
              <span class="badge badge-danger">Inactive</span>
            @endif
          </td>
          <td>
            <a href="{{ route('settings.diagnosis.edit',$d->id) }}"
               class="btn btn-sm btn-info">Edit</a>

            <form method="POST"
                  action="{{ route('settings.diagnosis.destroy',$d->id) }}"
                  style="display:inline"
                  onsubmit="return confirm('Delete diagnosis?');">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $diagnosis->links() }}
  </div>
</div>

@stop
