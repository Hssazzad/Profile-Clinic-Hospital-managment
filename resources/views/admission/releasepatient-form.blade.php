@extends('adminlte::page')

@section('title', 'Release Patient')

@section('content_header')
  <h1>Release Patient</h1>
@stop

{{-- 🔹 Page-specific CSS --}}
@section('css')
<style>
  /* Main card look */
  .release-card {
      background:#fff;
      border:1px solid #e5e7eb;
      border-radius:.75rem;
      box-shadow:0 4px 10px rgba(15,23,42,.05);
  }

  /* Label spacing */
  .release-card label {
      font-weight:500;
      margin-bottom:.25rem;
  }

  /* Help text */
  .release-help {
      font-size:.85rem;
      color:#6b7280;
  }

  /* Row that contains medicine inputs + Add button */
  .release-med-row {
      display:flex;
      flex-wrap:wrap;
      gap:.5rem;
      margin-bottom:.75rem;
  }

  .release-med-row > .field-wrap {
      flex:1 1 180px;
  }

  .release-med-row > .btn-wrap {
      flex:0 0 90px;
      display:flex;
      align-items:flex-end;
  }

  /* Make select/inputs consistent height */
  .release-card .form-control {
      padding:.4rem .55rem;
      font-size:.9rem;
  }

  /* Table styling */
  .release-table {
      margin-top:.5rem;
  }

  .release-table th,
  .release-table td {
      font-size:.9rem;
      vertical-align:middle;
  }

  .release-table thead th {
      background:#f3f4f6;
      border-bottom:2px solid #e5e7eb;
  }

  /* Mobile tweaks */
  @media (max-width: 767.98px) {
      .release-med-row {
          flex-direction:column;
      }
      .release-med-row > .btn-wrap {
          width:100%;
      }
      .release-med-row > .btn-wrap .btn {
          width:100%;
      }
  }
</style>
@endsection

{{-- 🔹 Main content --}}
@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card release-card">
  <div class="card-body">

    {{-- Release form --}}
    <form method="POST" action="{{ route('admission.releasepatient.store') }}" id="releaseForm">
      @csrf

      {{-- 🔸 Admitted Patient dropdown --}}
      <div class="form-group">
        <label>Admitted Patient</label>
        <select name="admission_id" id="admission_id" class="form-control" required>
          <option value="">-- Select Admitted Patient --</option>
          @foreach($admitted as $a)
            <option value="{{ $a->id }}">
              {{ $a->patientname }} | Ward {{ $a->ward }} | Bed {{ $a->bed_no }} | {{ $a->admit_date }}
            </option>
          @endforeach
        </select>
      </div>

      <hr>

      {{-- 🔸 Release medicines (AJAX) --}}
      <h5>Release Medicines (AJAX Save)</h5>
      <p class="release-help">
        Select medicine and click <strong>Add</strong>. Each row is saved immediately.
      </p>

      <div class="release-med-row">
        <div class="field-wrap">
          <label>Medicine</label>
          <select id="med_select" class="form-control">
            <option value="">-- Select Medicine --</option>
            @foreach($medicines as $m)
              <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="field-wrap">
          <label>Dose</label>
          <input type="text" id="med_dose" class="form-control" placeholder="1+0+1">
        </div>

        <div class="field-wrap">
          <label>Instruction</label>
          <input type="text" id="med_instruction" class="form-control" placeholder="After meal">
        </div>

        <div class="btn-wrap">
          <button type="button" id="btnAddMed" class="btn btn-secondary btn-sm btn-block">
            + Add
          </button>
        </div>
      </div>

      {{-- 🔸 Medicines list --}}
      <table class="table table-bordered release-table">
        <thead>
          <tr>
            <th style="width:40%">Medicine</th>
            <th style="width:20%">Dose</th>
            <th style="width:30%">Instruction</th>
            <th style="width:10%">#</th>
          </tr>
        </thead>
        <tbody id="medListBody">
          <tr>
            <td colspan="4" class="text-muted text-center">Select a patient first.</td>
          </tr>
        </tbody>
      </table>

      <hr>

      {{-- 🔸 Final release note --}}
      <div class="form-group">
        <label>Release Note</label>
        <textarea name="release_note" class="form-control" rows="3"
                  placeholder="Write summary, advice, follow-up..."></textarea>
      </div>

      <button class="btn btn-primary"
              onclick="return confirm('Confirm release of this patient?');">
        Release Patient
      </button>

    </form>
  </div>
</div>

@endsection {{-- 🔚 closes content section --}}

{{-- 🔹 Page JS --}}
@section('js')
<script>
$(function () {

    var ajaxUrl = "{{ route('admission.releasepatient.meds.ajax') }}";
    var token   = "{{ csrf_token() }}";

    function currentAdmissionId() {
        return $('#admission_id').val();
    }

    // 🔄 Load medicines list for selected admission
    function reloadMeds() {
        var aid = currentAdmissionId();

        if (!aid) {
            $('#medListBody').html(
                '<tr><td colspan="4" class="text-muted text-center">Select a patient first.</td></tr>'
            );
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: token,
                mode: 'list',
                admission_id: aid
            },
            success: function (res) {
                if (res.ok) {
                    $('#medListBody').html(res.html);
                } else {
                    alert(res.message || 'Failed to load medicines.');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error loading medicines.');
            }
        });
    }

    // ➕ Add medicine via AJAX
    function addMedicine() {
        var aid   = currentAdmissionId();
        var mid   = $('#med_select').val();
        var dose  = $('#med_dose').val().trim();
        var instr = $('#med_instruction').val().trim();

        if (!aid) {
            alert('Please select an admitted patient first.');
            return;
        }

        if (!mid && !dose && !instr) {
            alert('Fill at least one field (medicine, dose or instruction).');
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: token,
                mode: 'add',
                admission_id: aid,
                medicine_id: mid,
                dose: dose,
                instruction: instr
            },
            success: function (res) {
                if (res.ok) {
                    // clear inputs
                    $('#med_select').val('');
                    $('#med_dose').val('');
                    $('#med_instruction').val('');
                    // refresh list
                    $('#medListBody').html(res.html);
                } else {
                    alert(res.message || 'Failed to save medicine.');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error saving medicine.');
            }
        });
    }

    // 🗑 Delete medicine
    function deleteMedicine(id) {
        var aid = currentAdmissionId();
        if (!aid) return;

        if (!confirm('Remove this medicine?')) return;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: token,
                mode: 'delete',
                admission_id: aid,
                id: id
            },
            success: function (res) {
                if (res.ok) {
                    $('#medListBody').html(res.html);
                } else {
                    alert(res.message || 'Failed to delete medicine.');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error deleting medicine.');
            }
        });
    }

    // 🔔 Event bindings

    // when patient changes, load their meds
    $('#admission_id').on('change', function () {
        reloadMeds();
    });

    // Add button
    $('#btnAddMed').on('click', function () {
        addMedicine();
    });

    // Delete button (delegated)
    $('#medListBody').on('click', '.btn-delete-med', function () {
        var id = $(this).data('id');
        if (id) deleteMedicine(id);
    });

    // Initial state
    if (currentAdmissionId()) {
        reloadMeds();
    } else {
        $('#medListBody').html(
            '<tr><td colspan="4" class="text-muted text-center">Select a patient first.</td></tr>'
        );
    }
});
</script>
@endsection
