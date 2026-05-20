@php
  $pid       = (int)($pid ?? request('id'));
  $patientId = (int)($patient->id ?? request('patient'));
@endphp

<style>
  .card{background:#fff;border:1px solid #e5eaf3;border-radius:.75rem;padding:1rem;margin-bottom:1rem}
  .btn{padding:.5rem .9rem;border-radius:.5rem;border:1px solid #dbe3f0;background:#0ea5e9;color:#fff;cursor:pointer}
  .btn.secondary{background:#f8fafc;color:#0e1a2a}
  .btn.danger{background:#ef4444}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
</style>

<div class="card">
  <h3 style="margin-top:0">Finish</h3>
  <p style="color:#475569">You can print/download the current prescription, or start a new one.</p>

  <div class="grid2">
    <div>
      <h4 style="margin:.25rem 0">Current Prescription</h4>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <a class="btn secondary" href="{{ route('rx.wizard',['id'=>$pid,'patient'=>$patientId,'tab'=>'preview']) }}">? Back to Preview</a>
        {{-- If you have a PDF/print route, link it here --}}
        {{-- <a class="btn" href="{{ route('rx.print', $pid) }}" target="_blank">Print / PDF</a> --}}
      </div>
    </div>

    <div>
      <h4 style="margin:.25rem 0">Start New Prescription</h4>
      <div class="card" style="margin:0">
        <form method="post" action="{{ route('rx.finish.new') }}" style="display:flex;flex-direction:column;gap:.5rem">
          @csrf
          <input type="hidden" name="patient_id" value="{{ $patientId ?: '' }}">
          <label style="display:flex;align-items:center;gap:.5rem">
            <input type="checkbox" name="same_patient" value="1" {{ $patientId ? '' : 'disabled' }}>
            <span>Use the same patient ({{ $patient?->patientname ?? 'N/A' }})</span>
          </label>
          <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <button class="btn" type="submit">Create New Prescription</button>
            <a class="btn secondary" href="{{ route('rx.wizard',['tab'=>'patients']) }}">Start Fresh (no patient)</a>
          </div>
          @if(!$patientId)
            <div style="font-size:.9rem;color:#64748b">Tip: To reuse the same patient, go back to Patients and select/save a patient first.</div>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>

<div style="display:flex;justify-content:space-between;gap:.75rem">
  <a class="btn secondary" href="{{ route('rx.wizard',['id'=>$pid,'patient'=>$patientId,'tab'=>'preview']) }}">? Back</a>
  <a class="btn" href="{{ route('rx.wizard',['tab'=>'patients']) }}">Go to New (Patients)</a>
</div>
