@extends('adminlte::page')

@section('title', 'Investigation List')

@section('content_header')
  <h1 class="text-primary">Investigation List</h1>
@stop

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('settings.investigation.create') }}" class="btn btn-primary mb-3">
  + Add Investigation
</a>

<div class="card">
  <div class="table-responsive p-0">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Category</th>
          <th>Description</th>
          <th>Active</th>
          <th width="140">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($investigations as $i => $inv)
          <tr>
            <td>{{ $investigations->firstItem() + $i }}</td>
            <td>{{ $inv->name }}</td>
            <td>{{ $inv->category }}</td>
            <td style="max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              {{ $inv->description }}
            </td>
            <td>
              @if($inv->active)
                <span class="badge badge-success">Active</span>
              @else
                <span class="badge badge-danger">Inactive</span>
              @endif
            </td>
            <td>
              <a href="{{ route('settings.investigation.edit', $inv->id) }}"
                 class="btn btn-sm btn-info">Edit</a>

              <form action="{{ route('settings.investigation.destroy', $inv->id) }}"
                    method="post"
                    style="display:inline-block"
                    onsubmit="return confirm('Delete this investigation?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">
              No investigations found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($investigations->hasPages())
    <div class="card-footer">
      {{ $investigations->links() }}
    </div>
  @endif
</div>

@stop
