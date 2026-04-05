@extends('adminlte::page')

@section('title', 'Admission List')

@section('content_header')
  <h1>Admission List</h1>
@stop

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- 🔎 Search bar --}}
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admission.list') }}" class="form-inline">
      <div class="form-group mr-2">
        <input type="text"
               name="q"
               value="{{ $q ?? '' }}"
               class="form-control"
               style="min-width:250px"
               placeholder="Search by name or mobile">
      </div>
      <button class="btn btn-primary btn-sm mr-2">Search</button>
      <a href="{{ route('admission.list') }}" class="btn btn-secondary btn-sm">Reset</a>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Patient</th>
          <th>Mobile</th>
          <th>Admit Date</th>
          <th>Discharge Date</th>
          <th>Ward</th>
          <th>Bed</th>
          <th>Status</th>
          <th>Slip / Rx</th>
          <th>Discharge</th>
        </tr>
      </thead>

      <tbody>
        @forelse($admissions as $ad)
          <tr>
            <td>{{ $loop->iteration + ($admissions->currentPage()-1)*$admissions->perPage() }}</td>
            <td>{{ $ad->patientname }}</td>
            <td>{{ $ad->mobile_no ?? $ad->mobileno }}</td>
            <td>{{ $ad->admit_date }}</td>
            <td>{{ $ad->discharge_date ?? '-' }}</td>
            <td>{{ $ad->ward }}</td>
            <td>{{ $ad->bed_no }}</td>
            <td>
              @if($ad->status == 1)
                <span class="badge badge-success">Admitted</span>
              @elseif($ad->status == 2)
                <span class="badge badge-secondary">Discharged</span>
              @else
                <span class="badge badge-light">Unknown</span>
              @endif
            </td>

            {{-- 🔹 Slip + Admission Rx + Discharge Rx --}}
            <td style="min-width:180px">
              <a href="{{ route('admission.print', $ad->id) }}"
                 class="btn btn-xs btn-outline-primary mb-1">
                Slip
              </a>

              <a href="{{ route('admission.rx.admit', $ad->id) }}"
                 class="btn btn-xs btn-outline-success mb-1">
                Admit Rx
              </a>

              <a href="{{ route('admission.rx.discharge', $ad->id) }}"
                 class="btn btn-xs btn-outline-info mb-1 {{ !$ad->discharge_date ? 'disabled' : '' }}">
                Discharge Rx
              </a>
            </td>

            {{-- 🔹 Discharge form --}}
            <td>
              @if($ad->status == 1)
                <form method="POST"
                      action="{{ route('admission.discharge', $ad->id) }}"
                      onsubmit="return confirm('Discharge this patient?');">
                  @csrf
                  <input type="date"
                         name="discharge_date"
                         value="{{ now()->toDateString() }}"
                         class="form-control form-control-sm mb-1"
                         required>
                  <input type="text"
                         name="discharge_note"
                         class="form-control form-control-sm mb-1"
                         placeholder="Note (optional)">
                  <button class="btn btn-sm btn-danger btn-block">Discharge</button>
                </form>
              @else
                <span class="text-muted">Already discharged</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center text-muted">No admissions found.</td>
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
