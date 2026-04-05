@extends('adminlte::page')

@section('title', 'Doctor List')

@section('content_header')
  <h1 class="text-primary">Doctor List</h1>
@stop

@section('content')

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="mb-3">
  <a href="{{ route('settings.doctor.create') }}" class="btn btn-primary">
    + Add New Doctor
  </a>
</div>

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Reg No</th>
          <th>Name</th>
          <th>Speciality</th>
          <th>Contact</th>
          <th>Posting</th>
          <th>Rate Code</th>
          <th width="120">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($doctors as $idx => $d)
          <tr>
            <td>{{ $doctors->firstItem() + $idx }}</td>
            <td>{{ $d->reg_no }}</td>
            <td>{{ $d->doctor_name }}</td>
            <td>{{ $d->speciality }}</td>
            <td>{{ $d->contact }}</td>
            <td>{{ $d->Posting }}</td>
            <td>{{ $d->RateCode }}</td>
            <td>
              <a href="{{ route('settings.doctors.edit', $d->id) }}"
                 class="btn btn-xs btn-info">
                Edit
              </a>

              <form action="{{ route('settings.doctors.destroy', $d->id) }}"
                    method="post"
                    style="display:inline-block"
                    onsubmit="return confirm('Are you sure to delete this doctor?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-danger">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted">
              No doctors found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($doctors->hasPages())
    <div class="card-footer">
      {{ $doctors->links() }}
    </div>
  @endif
</div>

@stop
