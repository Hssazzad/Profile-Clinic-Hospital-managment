{{-- resources/views/patients/admit.blade.php --}}

@if (!isset($layoutless))
    {{-- When used as a full page --}}
    @extends('adminlte::page')

    @section('title', 'Admit Patient')

    @section('content_header')
      <h1>Admit Patient</h1>
    @stop

    @section('content')
@endif

<div class="card">
  <div class="card-header">
    <h4 class="mb-0">Admit: {{ $patient->patientname }}
      <small class="text-muted">
        ({{ $patient->mobile_no ?? $patient->mobileno }})
      </small>
    </h4>
  </div>

  <div class="card-body">
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(isset($currentAdmission) && $currentAdmission)
      <div class="alert alert-warning">
        This patient is already admitted in
        <b>Ward:</b> {{ $currentAdmission->ward }},
        <b>Bed:</b> {{ $currentAdmission->bed_no }},
        <b>Admit Date:</b> {{ $currentAdmission->admit_date }}.
      </div>
    @endif

    <form method="POST" action="{{ route('patients.admit.store', $patient->id) }}">
      @csrf

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="admit_date">Admit Date</label>
            <input type="date" id="admit_date" name="admit_date"
                   value="{{ old('admit_date', now()->toDateString()) }}"
                   class="form-control @error('admit_date') is-invalid @enderror"
                   required>
            @error('admit_date')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="ward">Ward</label>
            <input type="text" id="ward" name="ward"
                   value="{{ old('ward') }}"
                   class="form-control @error('ward') is-invalid @enderror"
                   placeholder="e.g. Medicine, Surgery"
                   required>
            @error('ward')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="bed_no">Bed No.</label>
            <input type="text" id="bed_no" name="bed_no"
                   value="{{ old('bed_no') }}"
                   class="form-control @error('bed_no') is-invalid @enderror"
                   placeholder="e.g. B-12"
                   required>
            @error('bed_no')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="reason">Reason for Admission</label>
        <textarea id="reason" name="reason" rows="3"
                  class="form-control @error('reason') is-invalid @enderror"
                  placeholder="Short summary of complaint / diagnosis">{{ old('reason') }}</textarea>
        @error('reason')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ url()->previous() }}"
           class="btn btn-secondary">
          Back
        </a>

        <button type="submit" class="btn btn-primary">
          Save Admission
        </button>
      </div>
    </form>
  </div>
</div>

@if (!isset($layoutless))
    @endsection
@endif
