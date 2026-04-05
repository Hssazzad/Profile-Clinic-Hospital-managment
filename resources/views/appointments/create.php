@extends('adminlte::page')

@section('title', 'Create Appointment')

{{-- Enable AdminLTE Select2 plugin --}}
@section('plugins.Select2', true)

@push('css')
  {{-- Ensure Select2 fills the field width --}}
  <style>.select2-container{width:100%!important;}</style>
@endpush

@section('content_header')
  <h1 class="text-primary">Create Appointment</h1>
@stop

@section('content')
@php $today = now()->toDateString(); @endphp

<div class="container mt-3">

  {{-- Success --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">

      {{-- Inline error box for same patient/date etc. --}}
      <div id="ap_error" class="alert alert-danger d-none"></div>

      <form id="apForm" action="{{ route('appointments.store.page') }}" method="POST">
        @csrf

        {{-- Patient (Select2) --}}
        <div class="form-group">
          <label>Patient <span class="text-danger">*</span></label>
          <select name="patient_id" id="patient_id" class="form-control select2" required style="width:100%;">
            <option value="">Search patient...</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">
                {{ $p->patientname }} ({{ $p->patientcode }})
              </option>
            @endforeach
          </select>
        </div>

        {{-- Date --}}
        <div class="form-group">
          <label>Date <span class="text-danger">*</span></label>
          <input type="date" name="appointment_date" id="appointment_date"
                 class="form-control" min="{{ $today }}" required>
        </div>

        {{-- Serial (1..50 minus taken) --}}
        <div class="form-group">
          <label>Serial Number <span class="text-danger">*</span></label>
          <select name="serial_no" id="serial_no" class="form-control" disabled required>
            <option value="">Select date first</option>
          </select>
        </div>

        {{-- Remarks --}}
        <div class="form-group">
          <label>Remarks</label>
          <textarea name="remarks" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-primary">Save Appointment</button>
      </form>
    </div>
  </div>
</div>
@stop

@push('js')
<script>
  // Helper to show/hide inline error
  function apShowError(msg){
    var box = document.getElementById('ap_error');
    box.classList.remove('d-none');
    box.textContent = msg;
  }
  function apClearError(){
    var box = document.getElementById('ap_error');
    box.classList.add('d-none');
    box.textContent = '';
  }

  // Reset serial dropdown
  function resetSerial(){
    $('#serial_no').prop('disabled', true).html('<option value="">Select date first</option>');
  }

  // Initialize once DOM + jQuery is ready
  (function init(){
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', onReady);
    } else { onReady(); }

    function onReady(){
      // Ensure jQuery is present
      if (!window.jQuery) {
        console.error('jQuery not found. Ensure AdminLTE assets are loaded.');
        return;
      }


      // Bind date change
      $('#appointment_date').on('change', function(){
        apClearError();
        var date = $(this).val();
        var pid  = $('#patient_id').val();
        resetSerial();
        if(!date || !pid) return;

        // 1) Same patient same date check
        $.get("{{ route('appointments.checkPatientDate') }}", {date:date, patient_id:pid}, function(r){
          if(r && r.exists){
            apShowError('This patient already has an appointment on this date.');
            resetSerial();
            return;
          }

          // 2) Load available serials (1..50 minus taken)
          $.get("{{ route('appointments.availableSerials') }}", {date:date, limit:50}, function(res){
            $('#serial_no').empty();

            if(res && res.ok && Array.isArray(res.available) && res.available.length){
              $('#serial_no').append('<option value="">Select Serial</option>');
              res.available.forEach(function(n){
                $('#serial_no').append('<option value="'+n+'">'+n+'</option>');
              });
              $('#serial_no').prop('disabled', false);
            } else {
              $('#serial_no').append('<option value="">No serials available</option>');
              $('#serial_no').prop('disabled', true);
            }
          });
        });
      });

      // If patient changes, clear date/serial and error
      $('#patient_id').on('change', function(){
        apClearError();
        $('#appointment_date').val('');
        resetSerial();
      });
    }
  })();
</script>
@endpush
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