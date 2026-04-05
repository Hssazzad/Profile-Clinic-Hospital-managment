{{-- resources/views/admissions/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Admissions')

@section('content_header')
  <h1>Admission List</h1>
@stop

@section('content')
@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Patient</th>
          <th>Mobile</th>
          <th>Ward/Bed</th>
          <th>Admit Date</th>
          <th>Discharge Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      @forelse($admissions as $ad)
        <tr>
          <td>{{ $loop->iteration + ($admissions->currentPage()-1)*$admissions->perPage() }}</td>
          <td>{{ $ad->patientname }}</td>
          <td>{{ $ad->mobile_no ?? $ad->mobileno }}</td>
          <td>{{ $ad->ward }} / {{ $ad->bed_no }}</td>
          <td>{{ $ad->admit_date }}</td>
          <td>{{ $ad->discharge_date ?? '-' }}</td>
          <td>
            @if($ad->status == 1)
              <span class="badge badge-success">Admitted</span>
            @elseif($ad->status == 2)
              <span class="badge badge-secondary">Discharged</span>
            @else
              <span class="badge badge-light">Unknown</span>
            @endif
          </td>
          <td>
            @if($ad->status == 1)
              <form method="POST"
                    action="{{ route('admissions.discharge', $ad->id) }}"
                    onsubmit="return confirm('Discharge this patient?');">
                @csrf
                <input type="date" name="discharge_date"
                       value="{{ now()->toDateString() }}"
                       class="form-control form-control-sm mb-1" required>
                <button class="btn btn-sm btn-danger">
                  Discharge
                </button>
              </form>
            @else
              <span class="text-muted">No action</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No admissions found.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $admissions->links() }}
  </div>
</div>
@endsection
