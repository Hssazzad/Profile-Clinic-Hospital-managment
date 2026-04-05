@extends('adminlte::page')

@section('title', 'New Prescription')

@section('content_header')
  <h1 class="text-primary">Create Prescription</h1>
@stop

@section('content')
<div class="container mt-3">

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Fix the following:</strong>
      <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="{{ route('prescriptions.store') }}" method="post">
        @csrf

        {{-- =================== Patient =================== --}}
        <fieldset class="border rounded p-3 mb-3">
          <legend class="w-auto px-2 h6 mb-0">Patient</legend>

          <label for="patient_id" class="font-weight-bold">Select Patient</label>
          <select name="patient_id" id="patient_id" class="form-control" required>
            <option value="">-- Select patient --</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">
                {{ $p->name ?? 'Patient' }}{{ isset($p->code) ? ' — '.$p->code : '' }}{{ isset($p->mobile) ? ' — '.$p->mobile : '' }}
              </option>
            @endforeach
          </select>
          <small class="text-muted d-block mt-1">Choose the patient to proceed.</small>
        </fieldset>

        <div class="row">
          {{-- =================== Investigations =================== --}}
          <div class="col-12 col-lg-6 mb-3">
            <fieldset class="border rounded p-3 h-100">
              <legend class="w-auto px-2 h6 mb-0">Investigations</legend>

              <label for="investigation_select">Select Investigation(s)</label>
              <select id="investigation_select" name="investigation[]" class="form-control" multiple size="8">
                @foreach($investigations as $i)
                  <option value="{{ $i->name }}">{{ $i->name }}</option>
                @endforeach
              </select>

              <label for="investigation_note" class="mt-2">Notes (optional, applies to all selected)</label>
              <input type="text" id="investigation_note" name="investigation_note_all" class="form-control" placeholder="Notes (optional)">
              <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to choose multiple.</small>
            </fieldset>
          </div>

          {{-- =================== Diagnosis =================== --}}
          <div class="col-12 col-lg-6 mb-3">
            <fieldset class="border rounded p-3 h-100">
              <legend class="w-auto px-2 h6 mb-0">Diagnosis</legend>

              <label for="diagnosis_select">Select Diagnosis</label>
              <select id="diagnosis_select" name="diagnosis[]" class="form-control" multiple size="8">
                @foreach($diagnoses as $d)
                  <option value="{{ $d->name }}">{{ $d->name }}</option>
                @endforeach
              </select>
              <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to choose multiple.</small>
            </fieldset>
          </div>

          {{-- =================== Complaints =================== --}}
          <div class="col-12 col-lg-6 mb-3">
            <fieldset class="border rounded p-3 h-100">
              <legend class="w-auto px-2 h6 mb-0">Chief Complaint(s)</legend>

              <label for="complain_select">Select Complaint(s)</label>
              <select id="complain_select" name="chief_complaint[]" class="form-control" multiple size="8">
                @foreach($complaints as $c)
                  <option value="{{ $c->name }}">{{ $c->name }}</option>
                @endforeach
              </select>
              <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to choose multiple.</small>
            </fieldset>
          </div>

          {{-- =================== Medicines =================== --}}
          <div class="col-12 col-lg-6 mb-3">
            <fieldset class="border rounded p-3 h-100">
              <legend class="w-auto px-2 h6 mb-0">Medicines</legend>

              <label for="medicine_id" class="font-weight-bold">Select Medicine</label>
              <select id="medicine_id" name="medicine_id[]" class="form-control" multiple size="8">
                @foreach($medicines as $m)
                  <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
              </select>
              <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to choose multiple.</small>

              <div class="row mt-3">
                <div class="col-md-4 mb-2">
                  <label for="med_strength">Strength</label>
                  <input type="text" id="med_strength" name="strength[]" class="form-control" placeholder="e.g., 500 mg">
                </div>
                <div class="col-md-4 mb-2">
                  <label for="med_dose">Dose</label>
                  <input type="text" id="med_dose" name="dose[]" class="form-control" placeholder="e.g., 1 tab">
                </div>
                <div class="col-md-4 mb-2">
                  <label for="med_route">Route</label>
                  <input type="text" id="med_route" name="route[]" class="form-control" placeholder="e.g., oral">
                </div>
                <div class="col-md-4 mb-2">
                  <label for="med_frequency">Frequency</label>
                  <input type="text" id="med_frequency" name="frequency[]" class="form-control" placeholder="e.g., TID">
                </div>
                <div class="col-md-4 mb-2">
                  <label for="med_duration">Duration</label>
                  <input type="text" id="med_duration" name="duration[]" class="form-control" placeholder="e.g., 5 days">
                </div>
                <div class="col-md-4 mb-2">
                  <label for="med_timing">Timing</label>
                  <input type="text" id="med_timing" name="timing[]" class="form-control" placeholder="e.g., after meal">
                </div>
              </div>

              <small class="text-muted d-block">If you select multiple medicines, you can submit the same strength/dose/etc. for all (server-side can explode rows if needed).</small>
            </fieldset>
          </div>
        </div>

        {{-- =================== Footer =================== --}}
        <fieldset class="border rounded p-3">
          <legend class="w-auto px-2 h6 mb-0">Prescription Details</legend>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="prescribed_on">Prescribed On</label>
              <input type="date" id="prescribed_on" name="prescribed_on" class="form-control" value="{{ old('prescribed_on', now()->toDateString()) }}">
            </div>
            <div class="form-group col-md-4">
              <label for="doctor_name">Doctor Name</label>
              <input type="text" id="doctor_name" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
            </div>
            <div class="form-group col-md-4">
              <label for="doctor_reg_no">Doctor Reg. No</label>
              <input type="text" id="doctor_reg_no" name="doctor_reg_no" class="form-control" value="{{ old('doctor_reg_no') }}">
            </div>
          </div>
        </fieldset>

        <div class="mt-3 d-flex flex-wrap">
          <button class="btn btn-primary mr-2 mb-2">Save Prescription</button>
          <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-2">Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('css')
<style>
  .form-row-3{display:flex;flex-wrap:wrap;align-items:center}
  .form-row-3 .label-col{flex:0 0 100%;max-width:100%;font-weight:600;margin-bottom:.25rem}
  .form-row-3 .colon-col{flex:0 0 10px;max-width:10px;text-align:center}
  .form-row-3 .input-col{flex:0 0 100%;max-width:100%}
  @media(min-width:768px){.form-row-3 .label-col{flex-basis:220px;max-width:220px;margin-bottom:0}.form-row-3 .input-col{flex:1 1 auto;max-width:none}}
  @media(min-width:992px){.form-row-3 .label-col{flex-basis:200px;max-width:200px}}
  .table .thead-light th{background:#f8f9fa}
  fieldset legend{font-weight:600}
</style>
@endsection
