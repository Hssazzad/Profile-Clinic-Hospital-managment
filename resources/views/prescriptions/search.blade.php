{{-- resources/views/prescriptions/search.blade.php --}}
@extends('adminlte::page')

@section('title','Search Prescription')

@section('content_header')
  <h1 class="mb-0">Search Prescription</h1>
@stop

@section('content')

<style>
  .rx-search-card{
      background:#fff;
      border-radius:.75rem;
      box-shadow:0 1px 3px rgba(15,23,42,.06);
      border:1px solid #e5e7eb;
      margin-bottom:1rem;
  }
  .rx-search-card .card-body{ padding:1rem 1.2rem; }

  #previewArea{
      margin-top:1rem;
  }
</style>

{{-- ===================== PATIENT SEARCH FORM ===================== --}}
<div class="card rx-search-card">
  <div class="card-body">
    <form method="GET" action="{{ route('rx.search') }}">
      <div class="form-group">
        <label>Patient</label>
        <select name="patient" class="form-control">
          <option value="">-- Select Patient --</option>
          @foreach($patients as $p)
            <option value="{{ $p->id }}" {{ (int)$patientId === (int)$p->id ? 'selected' : '' }}>
              {{ $p->patientname }} ({{ $p->mobile_no ?? $p->mobileno }})
            </option>
          @endforeach
        </select>
      </div>
      <button class="btn btn-primary">Search</button>
    </form>
  </div>
</div>

{{-- ===================== PRESCRIPTION LIST ===================== --}}
@if($selectedPatient)
  <div class="card rx-search-card">
    <div class="card-body">
      <h4 class="mb-3">Prescriptions of {{ $selectedPatient->patientname }}</h4>

      @if($prescriptions->isEmpty())
        <p>No prescriptions found.</p>
      @else
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th style="width:140px;">Date</th>
              <th>Doctor</th>
              <th style="width:150px;">Preview</th>
            </tr>
          </thead>
          <tbody>
          @foreach($prescriptions as $rx)
            <tr>
              <td>{{ $rx->id }}</td>
              <td>{{ \Carbon\Carbon::parse($rx->prescribed_on ?? $rx->created_at)->format('d-m-Y') }}</td>
              <td>{{ $rx->doctor_name ?? '—' }}</td>
              <td>
                <button type="button"
                        class="btn btn-sm btn-success btn-preview"
                        data-id="{{ $rx->id }}">
                  Preview & Inline PDF
                </button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
@endif

{{-- ===================== INLINE PREVIEW (LOADED BY AJAX) ===================== --}}
<div id="previewArea"></div>

@stop

@push('js')
<script>
// jQuery should already be loaded by AdminLTE. If not, include it in your layout.
$(function(){

    $('.btn-preview').on('click', function(e){
        e.preventDefault();

        let id  = $(this).data('id');
        let url = '{{ url("prescriptions/preview-ajax") }}/' + id;

        // Optional: highlight selected row
        $('.btn-preview').removeClass('btn-outline-dark').addClass('btn-success');
        $(this).removeClass('btn-success').addClass('btn-outline-dark');

        // Show loading indicator
        $('#previewArea').html(
            '<div class="card rx-search-card">'+
              '<div class="card-body text-center">'+
                '<div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;">' +
                  '<span class="sr-only">Loading...</span>' +
                '</div>'+
                '<div class="mt-2">Loading prescription preview...</div>'+
              '</div>'+
            '</div>'
        );

        $.ajax({
            url: url,
            type: 'GET',
            success: function(html){
                $('#previewArea').hide().html(html).fadeIn(200);

                // Smooth scroll to preview
                $('html, body').animate({
                    scrollTop: $('#previewArea').offset().top - 70
                }, 400);
            },
            error: function(xhr){
                let msg = 'Preview failed.';
                if (xhr.status === 404) msg = 'Prescription not found.';
                $('#previewArea').html(
                    '<div class="alert alert-danger mb-0">'+msg+'</div>'
                );
            }
        });
    });

});
</script>
@endpush
