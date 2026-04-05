{{-- resources/views/admission/admitpatient.blade.php --}}
@extends('adminlte::page')

{{-- Enable Select2 plugin from AdminLTE --}}
@section('plugins.Select2', true)

@section('title', 'Admit Patient')

@push('css')
<style>
  /* Select2 Custom Bootstrap 4 Styling */
  .select2-container--bootstrap4 .select2-selection {
      border: 1px solid #6b7280 !important;
      border-radius: 6px !important;
      min-height: 38px !important;
      padding: 4px !important;
  }
  .select2-container--bootstrap4.select2-container--focus .select2-selection {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 1px #2563eb33 !important;
  }
  .select2-results__option--highlighted {
      background: #0ea5e9 !important;
  }
</style>
@endpush

@section('content_header')
    <h1 class="text-primary font-weight-bold">Admit Patient</h1>
@stop

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <b><i class="fas fa-exclamation-triangle"></i> Please fix the following:</b>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-outline card-primary shadow">
        <div class="card-body">

            <form method="POST" action="{{ route('admission.admitpatient.store') }}">
                @csrf

                {{-- 1) Select patient --}}
                <div class="form-group">
                    <label for="patient_id"><b>Select Patient <span class="text-danger">*</span></b></label>
                    <select name="patient_id" id="patient_id"
                            class="form-control select2 @error('patient_id') is-invalid @enderror"
                            style="width: 100%;" required>
                        <option value="">-- Search by Name or Mobile --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->patientname }} ({{ $p->mobile_no ?? 'No Mobile' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr>

                {{-- 2) Select Template --}}
                <div class="form-group">
                    <label for="template"><b>Select Template <span class="text-danger">*</span></b></label>
                    <select name="template" id="template"
                            class="form-control select2 @error('template') is-invalid @enderror"
                            style="width: 100%;" required>
                        <option value="">-- Select Admission Reason Template --</option>
                        @foreach($templates as $t) {{-- কন্ট্রোলার অনুযায়ী $templates ব্যবহার করা হয়েছে --}}
                            <option value="{{ $t->templateid }}" {{ old('template') == $t->templateid ? 'selected' : '' }}>
                                {{ $t->reason }} [{{ $t->templateid }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr>

                {{-- 3) Admission details --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="admit_date">Admit Date</label>
                            <input type="date" id="admit_date" name="admit_date"
                                   value="{{ old('admit_date', now()->toDateString()) }}"
                                   class="form-control @error('admit_date') is-invalid @enderror"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ward">Ward</label>
                            <input type="text" id="ward" name="ward"
                                   value="{{ old('ward') }}"
                                   class="form-control"
                                   placeholder="e.g. Medicine, Surgery">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bed_no">Bed No.</label>
                            <input type="text" id="bed_no" name="bed_no"
                                   value="{{ old('bed_no') }}"
                                   class="form-control"
                                   placeholder="e.g. B-12">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Admission Remarks / Details</label>
                    <textarea id="reason" name="reason" rows="3"
                              class="form-control"
                              placeholder="Additional diagnosis details...">{{ old('reason') }}</textarea>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Back</a>
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save"></i> Save & Print Admission Slip
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // Initialize Select2 for all select elements with class .select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true
        });

        // Template select হলে reason টেক্সটবক্সে অটো ফিল করার লজিক (ঐচ্ছিক)
        $('#template').on('change', function() {
            let selectedText = $(this).find('option:selected').text().split('[')[0].trim();
            if(selectedText && !$('#reason').val()){
                $('#reason').val(selectedText);
            }
        });
    });
</script>
@endsection