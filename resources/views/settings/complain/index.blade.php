@extends('adminlte::page')

@section('title', 'Complain List')

@section('content_header')
  <h1 class="text-primary">Common Complains</h1>
@stop

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('settings.complain.create') }}" class="btn btn-primary mb-3">
  + Add Complain
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
          <th width="140">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($complains as $i => $c)
          <tr>
            <td>{{ $complains->firstItem() + $i }}</td>
            <td>{{ $c->code }}</td>
            <td>{{ $c->name }}</td>
            <td>
              @if($c->active)
                <span class="badge badge-success">Active</span>
              @else
                <span class="badge badge-danger">Inactive</span>
              @endif
            </td>
            <td>
              <a href="{{ route('settings.complain.edit', $c->id) }}"
                 class="btn btn-sm btn-info">Edit</a>

              <form action="{{ route('settings.complain.destroy', $c->id) }}"
                    method="post"
                    style="display:inline-block"
                    onsubmit="return confirm('Delete this complain?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted">
              No complains found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($complains->hasPages())
    <div class="card-footer">
      {{ $complains->links() }}
    </div>
  @endif
</div>

@stop
