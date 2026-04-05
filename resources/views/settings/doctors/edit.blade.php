@extends('adminlte::page')

@section('title', 'Edit Doctor')

@section('content_header')
  <h1 class="text-primary">Edit Doctor</h1>
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
    <form action="{{ route('settings.doctors.update', $doctor->id) }}" method="post">
      @csrf
      @method('PUT')

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Reg No *</label>
          <input type="text" name="reg_no" class="form-control"
                 value="{{ old('reg_no', $doctor->reg_no) }}" required maxlength="12">
        </div>

        <div class="form-group col-md-8">
          <label>Doctor Name *</label>
          <input type="text" name="doctor_name" class="form-control"
                 value="{{ old('doctor_name', $doctor->doctor_name) }}" required maxlength="50">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Speciality *</label>
          <input type="text" name="speciality" class="form-control"
                 value="{{ old('speciality', $doctor->speciality) }}" required maxlength="25">
        </div>

        <div class="form-group col-md-4">
          <label>Contact *</label>
          <input type="text" name="contact" class="form-control"
                 value="{{ old('contact', $doctor->contact) }}" required maxlength="12">
        </div>

        <div class="form-group col-md-4">
          <label>Posting *</label>
          <input type="text" name="Posting" class="form-control"
                 value="{{ old('Posting', $doctor->Posting) }}" required maxlength="25">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Rate Code *</label>
          <input type="text" name="RateCode" class="form-control"
                 value="{{ old('RateCode', $doctor->RateCode) }}" required maxlength="12">
        </div>
      </div>

      <button class="btn btn-primary">Update Doctor</button>
      <a href="{{ route('settings.doctors.index') }}" class="btn btn-secondary">
        Back to List
      </a>
    </form>
  </div>
</div>

@stop
