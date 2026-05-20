@extends('adminlte::page')

@section('title', 'Prescription View')

@section('content_header')
  <h1 class="text-primary">Prescription #{{ $rx->id }}</h1>
@stop

@section('content')
<style>
  .rx-toggle-btns{
      margin-bottom: 1rem;
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
  }
  .rx-toggle-btns .btn.active{
      box-shadow: 0 0 0 2px rgba(37,99,235,.3);
  }
  #html-view, #pdf-view{
      background:#fff;
      border:1px solid #e5e7eb;
      border-radius:.75rem;
      padding:1rem;
  }
  #pdfFrame{
      width: 100%;
      height: 80vh;
      border: none;
  }
</style>

<div class="mb-2">
    <a href="{{ route('prescriptions.search', ['patient' => $patient->id ?? null]) }}"
       class="btn btn-sm btn-secondary">
        ? Back to Search
    </a>
</div>

<div class="rx-toggle-btns">
    <button type="button" id="btnHtml" class="btn btn-sm btn-primary active">
        HTML View
    </button>
    <button type="button" id="btnPdf" class="btn btn-sm btn-secondary">
        PDF View
    </button>
</div>

{{-- ?? HTML VIEW --}}
<div id="html-view">
    <div class="d-flex justify-content-between mb-2">
        <div>
            <h3 style="margin:0;">{{ $patient->patientname ?? 'Unknown Patient' }}</h3>
            <small>
                Age: {{ $patient->age ?? '-' }},
                Mobile: {{ $patient->mobileno ?? '-' }}
            </small><br>
            <small>RX Date: {{ \Carbon\Carbon::parse($rx->created_at)->format('d-m-Y') }}</small>
        </div>
        <div class="text-right">
            <strong>Prescription #{{ $rx->id }}</strong><br>
            <small>Doctor: {{ $rx->doctor_name ?? '' }}</small>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <h5>Chief Complaints</h5>
            <ul>
                @forelse($complaints as $c)
                    <li>{{ $c->complaint }}</li>
                @empty
                    <li>No complaints found.</li>
                @endforelse
            </ul>

            <h5>Diagnosis</h5>
            <ul>
                @forelse($diagnoses as $d)
                    <li>{{ $d->diagnosis }}</li>
                @empty
                    <li>No diagnosis found.</li>
                @endforelse
            </ul>

            <h5>Investigations</h5>
            <ul>
                @forelse($investigations as $i)
                    <li>{{ $i->name }} {{ $i->note ? ' - '.$i->note : '' }}</li>
                @empty
                    <li>No investigations advised.</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-6">
            <h5>Medicines</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Dose</th>
                        <th>Duration</th>
                        <th>Timing</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $m)
                        <tr>
                            <td>{{ $m->medicine_name }} {{ $m->strength }}</td>
                            <td>{{ $m->dose }}</td>
                            <td>{{ $m->duration }}</td>
                            <td>{{ $m->timing }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No medicines added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ?? PDF VIEW (iframe) --}}
<div id="pdf-view" style="display:none;">
    <iframe
        id="pdfFrame"
        src="{{ route('prescriptions.pdf', $rx->id) }}"
        title="Prescription PDF">
    </iframe>
</div>
@stop

@push('js')
<script>
(function () {
    const btnHtml = document.getElementById('btnHtml');
    const btnPdf  = document.getElementById('btnPdf');
    const htmlView = document.getElementById('html-view');
    const pdfView  = document.getElementById('pdf-view');

    function activateHtml(){
        htmlView.style.display = 'block';
        pdfView.style.display  = 'none';
        btnHtml.classList.add('active','btn-primary');
        btnHtml.classList.remove('btn-secondary');
        btnPdf.classList.remove('active','btn-primary');
        btnPdf.classList.add('btn-secondary');
    }

    function activatePdf(){
        htmlView.style.display = 'none';
        pdfView.style.display  = 'block';
        btnPdf.classList.add('active','btn-primary');
        btnPdf.classList.remove('btn-secondary');
        btnHtml.classList.remove('active','btn-primary');
        btnHtml.classList.add('btn-secondary');
    }

    btnHtml.addEventListener('click', activateHtml);
    btnPdf.addEventListener('click', activatePdf);
})();
</script>
@endpush
