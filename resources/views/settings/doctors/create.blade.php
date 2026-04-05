@extends('adminlte::page')

@section('title', 'Add Doctor')

@section('content_header')
  <h1 class="text-primary">Add Doctor</h1>
@stop

@section('content')

@if ($errors->any())
  <div class="alert alert-danger">
    <b>Fix the following errors:</b>
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-body">
    <form action="{{ route('settings.doctor.store') }}" method="post">
      @csrf

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Reg No *</label>
          <input type="text" name="reg_no" class="form-control"
                 value="{{ old('reg_no') }}" required maxlength="12">
        </div>

        <div class="form-group col-md-8">
          <label>Doctor Name *</label>
          <input type="text" name="doctor_name" class="form-control"
                 value="{{ old('doctor_name') }}" required maxlength="50">
        </div>
      </div>

      <div class="form-row">
       <div class="form-group col-md-4">
          <label>Speciality <span class="text-danger">*</span></label>

          <select name="speciality"
                  class="form-control select2"
                  required>
              <option value="">-- Select Speciality --</option>

              @foreach($specialities as $sp)
                  <option value="{{ $sp->id }}"
                      {{ old('speciality') == $sp->id ? 'selected' : '' }}>
                      {{ $sp->name }}
                  </option>
              @endforeach
          </select>
      </div>


        <div class="form-group col-md-4">
          <label>Contact *</label>
          <input type="text" name="contact" class="form-control"
                 value="{{ old('contact') }}" required maxlength="12">
        </div>

        <div class="form-group col-md-4">
          <label>Posting *</label>
          <input type="text" name="Posting" class="form-control"
                 value="{{ old('Posting') }}" required maxlength="25">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Rate Code *</label>
          <input type="text" name="RateCode" class="form-control"
                 value="{{ old('RateCode') }}" required maxlength="12">
        </div>
      </div>

      <button class="btn btn-primary">Save Doctor</button>
      <a href="{{ route('settings.doctors.index') }}" class="btn btn-secondary">
        Back to List
      </a>
    </form>
  </div>
</div>

@stop
@push('js')
{{-- Select2 CDN --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function() {
    // Activate Select2 on all dropdowns
    $('.select2').select2({
       
        width: '100%',
       
       
    });


});
</script>
@endpush