@extends('adminlte::page')

@section('title', 'Edit Patient')

@section('content_header')
  <h1 class="text-primary">Edit Patient (???? ???? ?????)</h1>
@stop

@section('content')

{{-- ======================= Reference Modal ======================= --}}
<div class="modal fade" id="refModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add Reference Person</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="refMsg" class="small"></div>

        <div class="mb-2">
          <label class="form-label">Type</label>
          <select id="ref_type_modal" class="form-control">
            <option value="">-- Select Type --</option>
            <option value="OfficeEmployee">Office Employee</option>
            <option value="PCNurse">PC / Nurse</option>
            <option value="MidWife">Mid Wife</option>
            <option value="Others">Others</option>
          </select>
        </div>

        <div class="mb-2">
          <label class="form-label">Name</label>
          <input type="text" id="ref_name_modal" class="form-control" placeholder="Reference person name">
        </div>

        <div class="mb-2">
          <label class="form-label">Mobile</label>
          <input type="text" id="ref_mobile_modal" class="form-control" placeholder="01xxxxxxxxx">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btnSaveRef">Save</button>
      </div>

    </div>
  </div>
</div>

{{-- ======================= Location Modal ======================= --}}
<div class="modal fade" id="locModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="locModalTitle">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="locMsg" class="small mb-2"></div>

        <input type="hidden" id="loc_kind">
        <div class="mb-2">
          <label class="form-label">Name</label>
          <input type="text" id="loc_name" class="form-control" placeholder="Enter name">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btnSaveLoc">Save</button>
      </div>

    </div>
  </div>
</div>

<div class="container mt-3">

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Whoops!</strong> Please fix the following errors:
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">

      {{-- ? update route --}}
      <form action="{{ route('patients.updatepatient', $patient->id) }}" method="post" id="patientForm" enctype="multipart/form-data">
        @csrf

        <table class="table table-borderless align-middle w-100" style="max-width:900px;">
          <tbody>

            {{-- Photo --}}
            <tr>
              <td class="fw-semibold" style="width:220px;">Photo</td>
              <td style="width:10px;">:</td>
              <td>
                @php
                  // ? Change photo column if needed (photo_path / photo / image)
                  $storedPath = old('photo_path', $patient->photo_path ?? null);
                  $photoUrl   = $storedPath ? asset('storage/'.$storedPath) : asset('uploads/photos/no_image.png');
                @endphp

                <img id="previewImg"
                     src="{{ $photoUrl }}"
                     style="width:150px;height:150px;border:1px solid #ccc;object-fit:cover;margin:8px 0;">
                <br>

                <input type="file" name="photo" id="photoInput" accept="image/*" onchange="previewImage(this)">

                @error('photo')
                  <div style="color:red;margin-top:6px;">{{ $message }}</div>
                @enderror
              </td>
            </tr>

            {{-- Patient code --}}
            <tr>
              <td class="fw-semibold" style="width:220px;">Patient Code (???? ???)</td>
              <td style="width:10px;">:</td>
              <td>
                <input type="text"
                       name="patientcode"
                       class="form-control"
                       value="{{ old('patientcode', $patient->patientcode) }}"
                       readonly>
              </td>
            </tr>

            {{-- Name --}}
            <tr>
              <td class="fw-semibold">Patient Name (???) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
              <td>:</td>
              <td><input type="text" name="patientname" class="form-control" required value="{{ old('patientname', $patient->patientname) }}"></td>
            </tr>

            {{-- Father --}}
            <tr>
              <td class="fw-semibold">Patient father Name (????? ???) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
              <td>:</td>
              <td><input type="text" name="patientfather" class="form-control" required value="{{ old('patientfather', $patient->patientfather) }}"></td>
            </tr>

            {{-- Mobiles --}}
            <tr>
              <td class="fw-semibold">Own Mobile (????? ??????) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
              <td>:</td>
              <td><input type="text" id="mobile_no" name="mobile_no" class="form-control" value="{{ old('mobile_no', $patient->mobile_no) }}"></td>
            </tr>

            <tr>
              <td class="fw-semibold">Spouse Mobile (??????/??????? ??????)</td>
              <td>:</td>
              <td><input type="text" id="spomobile_no" name="spomobile_no" class="form-control" value="{{ old('spomobile_no', $patient->spomobile_no) }}"></td>
            </tr>

            <tr>
              <td class="fw-semibold">Relative Mobile (????????? ??????)</td>
              <td>:</td>
              <td><input type="text" id="relmobile_no" name="relmobile_no" class="form-control" value="{{ old('relmobile_no', $patient->relmobile_no) }}"></td>
            </tr>

            {{-- Email --}}
            <tr>
              <td class="fw-semibold">Email</td>
              <td>:</td>
              <td><input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}"></td>
            </tr>

            {{-- DOB --}}
            <tr>
              <td class="fw-semibold">Date of Birth (?????????) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
              <td>:</td>
              <td><input type="date" name="date_of_birth" id="dob" class="form-control" value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}"></td>
            </tr>

            {{-- Age --}}
            <tr>
              <td class="fw-semibold">Age (???) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
              <td>:</td>
              <td><input type="text" name="age" id="age" class="form-control" value="{{ old('age', $patient->age) }}" required></td>
            </tr>

            {{-- NID --}}
            <tr>
              <td class="fw-semibold">NID Number</td>
              <td>:</td>
              <td><input type="text" name="nid_number" class="form-control" value="{{ old('nid_number', $patient->nid_number) }}"></td>
            </tr>

            {{-- Gender --}}
            <tr>
              <td class="fw-semibold">
                Gender (?????)
                <span class="text-danger fw-bold" style="font-size:22px; line-height:1;">*</span>
              </td>
              <td>:</td>
              <td>
                @php($g = old('gender', $patient->gender))
                <select name="gender" class="form-select select2" required>
                  <option value="">-- Select --</option>
                  <option value="Male"   {{ $g === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ $g === 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other"  {{ $g === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </td>
            </tr>

            {{-- Blood group --}}
            <tr>
              <td class="fw-semibold">Blood Group (?????? ?????)</td>
              <td>:</td>
              <td>
                @php($bgv = old('blood_group', $patient->blood_group))
                <select name="blood_group" class="form-control select2" required>
                  <option value="">-- Select Blood Group --</option>
                  @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" {{ $bgv===$bg ? 'selected' : '' }}>{{ $bg }}</option>
                  @endforeach
                </select>
              </td>
            </tr>

            {{-- District --}}
            <tr>
              <td class="fw-semibold">District (????)</td>
              <td>:</td>
              <td class="d-flex gap-2 align-items-center">
                @php($dist = old('district', $patient->district))
                <select id="district" name="district" class="form-control select2" required>
                  <option value="">-- Select District --</option>
                  @foreach($districts as $d)
                    <option value="{{ $d->code }}" {{ (string)$dist === (string)$d->code ? 'selected' : '' }}>
                      {{ $d->name }}
                    </option>
                  @endforeach
                </select>

                <button type="button" class="btn btn-success btn-sm" id="btnAddDistrict">+</button>
              </td>
            </tr>

            {{-- Upozila --}}
            <tr>
              <td class="fw-semibold">Upozila</td>
              <td>:</td>
              <td class="d-flex gap-2 align-items-center">
                <select id="upozila" name="upozila" class="form-control select2" disabled>
                  <option value="">-- Select Upozila --</option>
                </select>
                <button type="button" class="btn btn-success btn-sm" id="btnAddUpozila" disabled>+</button>
              </td>
            </tr>

            {{-- Union --}}
            <tr>
              <td class="fw-semibold">Union</td>
              <td>:</td>
              <td class="d-flex gap-2 align-items-center">
                <select id="union" name="union" class="form-control select2" disabled>
                  <option value="">-- Select Union --</option>
                </select>
                <button type="button" class="btn btn-success btn-sm" id="btnAddUnion" disabled>+</button>
              </td>
            </tr>

            {{-- Village --}}
            <tr>
              <td class="fw-semibold">Village</td>
              <td>:</td>
              <td class="d-flex gap-2 align-items-center">
                <select id="village" name="village" class="form-control select2" disabled>
                  <option value="">-- Select Village --</option>
                </select>
                <button type="button" class="btn btn-success btn-sm" id="btnAddVillage" disabled>+</button>
              </td>
            </tr>

            {{-- Reference --}}
            <tr>
              <td class="fw-semibold">Reference Type</td>
              <td>:</td>
              <td>
                @php($rt = old('reference_type', $patient->reference_type))
                <select name="reference_type" id="reference_type" class="form-control">
                  <option value="">-- Select Type --</option>
                  <option value="Self"          {{ $rt==='Self' ? 'selected':'' }}>Self</option>
                  <option value="OfficeEmployee"{{ $rt==='OfficeEmployee' ? 'selected':'' }}>Office Employee</option>
                  <option value="PCNurse"       {{ $rt==='PCNurse' ? 'selected':'' }}>PC / Nurse</option>
                  <option value="MidWife"       {{ $rt==='MidWife' ? 'selected':'' }}>Mid Wife</option>
                  <option value="NOCOM"         {{ $rt==='NOCOM' ? 'selected':'' }}>No Commission</option>
                  <option value="Others"        {{ $rt==='Others' ? 'selected':'' }}>Others</option>
                </select>

                <button type="button" class="btn btn-success btn-sm" id="btnAddRef" title="Add Reference Person">+</button>
              </td>
            </tr>

            <tr id="personRow" style="display:none;">
              <td class="fw-semibold">Reference Person</td>
              <td>:</td>
              <td>
                <select name="reference_person" id="reference_person" class="form-control">
                  <option value="">-- Select Person --</option>
                </select>
              </td>
            </tr>

            <tr id="manualRow" style="display:none;">
              <td class="fw-semibold">Reference Name</td>
              <td>:</td>
              <td>
                <input type="text" name="reference_name" id="reference_name" class="form-control"
                       value="{{ old('reference_name', $patient->reference_name) }}"
                       placeholder="Enter Reference Name">
              </td>
            </tr>

            {{-- Notes --}}
            <tr>
              <td class="fw-semibold">Notes (????????)</td>
              <td>:</td>
              <td><textarea name="notes" rows="3" class="form-control">{{ old('notes', $patient->notes) }}</textarea></td>
            </tr>

          </tbody>
        </table>

        <div class="mt-3">
          <button class="btn btn-primary">Update</button>
          <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>
@stop

@push('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  // ? dependent select values: old() first, else patient DB
  const OLD_DISTRICT = @json(old('district', $patient->district));
  const OLD_UPOZILA  = @json(old('upozila',  $patient->upozila));
  const OLD_UNION    = @json(old('union',    $patient->union));
  const OLD_VILLAGE  = @json(old('village',  $patient->village));

  const OLD_REF_TYPE   = @json(old('reference_type', $patient->reference_type));
  const OLD_REF_PERSON = @json(old('reference_person', $patient->reference_person));
</script>

<script>
$(function () {
  const CSRF = "{{ csrf_token() }}";
  $(".select2").select2();

  // ======================= Address cascade =======================
  function resetUpozila(){
    $("#upozila").prop('disabled', true).html('<option value="">-- Select Upozila --</option>').trigger('change.select2');
    $("#btnAddUpozila").prop('disabled', true);
    resetUnion();
  }
  function resetUnion(){
    $("#union").prop('disabled', true).html('<option value="">-- Select Union --</option>').trigger('change.select2');
    $("#btnAddUnion").prop('disabled', true);
    resetVillage();
  }
  function resetVillage(){
    $("#village").prop('disabled', true).html('<option value="">-- Select Village --</option>').trigger('change.select2');
    $("#btnAddVillage").prop('disabled', true);
  }

  function loadUpozila(district, selected, cb){
    resetUpozila();
    if(!district) return;

    $("#upozila").html('<option value="">Loading...</option>').prop('disabled', true);
    $.post("{{ route('api.fetch_upozila') }}", { _token: CSRF, district: district }, function(html){
      $("#upozila").html(html).prop('disabled', false).trigger('change.select2');
      $("#btnAddUpozila").prop('disabled', false);
      $("#upozila").val(selected ? String(selected) : "").trigger('change');
      if(typeof cb === 'function') cb();
    }).fail(function(){
      $("#upozila").html('<option value="">Failed to load</option>').prop('disabled', true);
    });
  }

  function loadUnion(upozila, selected, cb){
    resetUnion();
    if(!upozila) return;

    $("#union").html('<option value="">Loading...</option>').prop('disabled', true);
    $.post("{{ route('api.fetch_union') }}", { _token: CSRF, upozila: upozila }, function(html){
      $("#union").html(html).prop('disabled', false).trigger('change.select2');
      $("#btnAddUnion").prop('disabled', false);
      $("#union").val(selected ? String(selected) : "").trigger('change');
      if(typeof cb === 'function') cb();
    }).fail(function(){
      $("#union").html('<option value="">Failed to load</option>').prop('disabled', true);
    });
  }

  function loadVillage(union, selected){
    resetVillage();
    if(!union) return;

    $("#village").html('<option value="">Loading...</option>').prop('disabled', true);
    $.post("{{ route('api.fetch_village') }}", { _token: CSRF, union: union }, function(html){
      $("#village").html(html).prop('disabled', false).trigger('change.select2');
      $("#btnAddVillage").prop('disabled', false);
      $("#village").val(selected ? String(selected) : "").trigger('change');
    }).fail(function(){
      $("#village").html('<option value="">Failed to load</option>').prop('disabled', true);
    });
  }

  $("#district").on("change", function(){ loadUpozila(this.value, null); });
  $("#upozila").on("change", function(){ loadUnion(this.value, null); });
  $("#union").on("change", function(){ loadVillage(this.value, null); });

  // ? initial load (edit): load chain using OLD_* values
  if (OLD_DISTRICT) {
    loadUpozila(OLD_DISTRICT, OLD_UPOZILA, function(){
      loadUnion(OLD_UPOZILA, OLD_UNION, function(){
        loadVillage(OLD_UNION, OLD_VILLAGE);
      });
    });
  } else {
    resetUpozila();
  }

  // ======================= Reference UI =======================
  function showRefUI(type){
    $("#personRow").hide();
    $("#manualRow").hide();
    if(!type) return;

    if(type === "Self" || type === "Others"){
      $("#manualRow").show();
      return;
    }
    $("#personRow").show();
  }

  function loadReferencePersons(type, selectedId=''){
    $("#reference_person").html('<option value="">-- Select Person --</option>');
    if(!type || type === 'Self' || type === 'Others') return;

    $("#reference_person").html('<option value="">Loading...</option>');
    $.post("{{ route('api.fetch_reference_person') }}", { _token: CSRF, reference_type: type }, function(html){
      $("#reference_person").html(html);
      if(selectedId) $("#reference_person").val(String(selectedId));
    }).fail(function(){
      $("#reference_person").html('<option value="">Failed to load</option>');
    });
  }

  $("#reference_type").on("change", function(){
    const type = this.value;
    showRefUI(type);
    loadReferencePersons(type);
  });

  // ? initial restore for edit
  if (OLD_REF_TYPE) {
    $("#reference_type").val(OLD_REF_TYPE);
    showRefUI(OLD_REF_TYPE);
    loadReferencePersons(OLD_REF_TYPE, OLD_REF_PERSON);
  }

  // + open modal
  $("#btnAddRef").on("click", function(){
    const currentType = $("#reference_type").val();
    if (currentType && currentType !== 'Self') $("#ref_type_modal").val(currentType);
    else $("#ref_type_modal").val('');

    $("#ref_name_modal").val('');
    $("#ref_mobile_modal").val('');
    $("#refMsg").html('');
    $("#refModal").modal('show');
  });

  // save reference from modal
  $("#btnSaveRef").on("click", function(){
    const ref_type = $("#ref_type_modal").val();
    const name     = $("#ref_name_modal").val();
    const mobile   = $("#ref_mobile_modal").val();

    $("#refMsg").html('');
    $.ajax({
      type: "POST",
      url: "{{ route('api.reference_person.store') }}",
      dataType: "json",
      data: { _token: CSRF, ref_type: ref_type, Name: name, Mobile: mobile },
      success: function(res){
        if (!res.ok) {
          $("#refMsg").html('<div class="text-danger">Failed to save.</div>');
          return;
        }
        $("#reference_type").val(res.type).trigger('change');
        loadReferencePersons(res.type, res.id);
        $("#refModal").modal('hide');
      },
      error: function(){
        $("#refMsg").html('<div class="text-danger">Validation failed / server error.</div>');
      }
    });
  });

  // ======================= Location Add (+) =======================
  function openLocModal(kind){
    $("#loc_kind").val(kind);
    $("#loc_name").val('');
    $("#locMsg").html('');

    let title = 'Add';
    if(kind==='district') title = 'Add District';
    if(kind==='upozila')  title = 'Add Upozila';
    if(kind==='union')    title = 'Add Union';
    if(kind==='village')  title = 'Add Village';

    $("#locModalTitle").text(title);
    $("#locModal").modal('show');
  }

  $("#btnAddDistrict").click(()=> openLocModal('district'));
  $("#btnAddUpozila").click(()=> openLocModal('upozila'));
  $("#btnAddUnion").click(()=> openLocModal('union'));
  $("#btnAddVillage").click(()=> openLocModal('village'));

  // enable/disable + buttons
  $("#district").on("change", function(){
    $("#btnAddUpozila").prop('disabled', !this.value);
    $("#btnAddUnion").prop('disabled', true);
    $("#btnAddVillage").prop('disabled', true);
  });
  $("#upozila").on("change", function(){
    $("#btnAddUnion").prop('disabled', !this.value);
    $("#btnAddVillage").prop('disabled', true);
  });
  $("#union").on("change", function(){
    $("#btnAddVillage").prop('disabled', !this.value);
  });

  // reload helper (kept same as your create)
  function reloadSelect(kind, parentIds, selectedId){
    if(kind === 'upozila'){ loadUpozila(parentIds.district, selectedId); return; }
    if(kind === 'union'){  loadUnion(parentIds.upozila, selectedId);   return; }
    if(kind === 'village'){loadVillage(parentIds.union, selectedId);   return; }

    if(kind === 'district'){
      // if you have api.fetch_district returning <option> list
      $.post("{{ route('api.fetch_district') }}", { _token: CSRF }, function(html){
        $("#district").html(html).trigger('change.select2');
        $("#district").val(String(selectedId)).trigger('change');
      });
      return;
    }
  }

  $("#btnSaveLoc").on("click", function(){
    const kind = $("#loc_kind").val();
    const name = ($("#loc_name").val() || '').trim();
    if(!name){
      $("#locMsg").html('<div class="text-danger">Name is required.</div>');
      return;
    }

    const district = $("#district").val();
    const upozila  = $("#upozila").val();
    const union    = $("#union").val();

    let storeUrl = '';
    let payload  = { _token: CSRF, name: name };

    if(kind === 'district'){
      storeUrl = "{{ route('api.district.store') }}";
    }
    if(kind === 'upozila'){
      if(!district){ $("#locMsg").html('<div class="text-danger">Select District first.</div>'); return; }
      storeUrl = "{{ route('api.upozila.store') }}";
      payload.district = district;
    }
    if(kind === 'union'){
      if(!upozila){ $("#locMsg").html('<div class="text-danger">Select Upozila first.</div>'); return; }
      storeUrl = "{{ route('api.union.store') }}";
      payload.upozila = upozila;
    }
    if(kind === 'village'){
      if(!union){ $("#locMsg").html('<div class="text-danger">Select Union first.</div>'); return; }
      storeUrl = "{{ route('api.village.store') }}";
      payload.union = union;
    }

    $.ajax({
      type: "POST",
      url: storeUrl,
      dataType: "json",
      data: payload,
      success: function(res){
        if(!res.ok){
          $("#locMsg").html('<div class="text-danger">'+(res.message || 'Failed')+'</div>');
          return;
        }
        reloadSelect(kind, { district, upozila, union }, res.id);
        $("#locModal").modal('hide');
      },
      error: function(){
        $("#locMsg").html('<div class="text-danger">Server error / validation failed.</div>');
      }
    });
  });

});
</script>

<script>
  // DOB -> Age
  $(function(){
    $("#dob").on("change", function () {
      const dob = $(this).val();
      if (!dob) return;

      const birthDate = new Date(dob);
      const today = new Date();

      let years  = today.getFullYear() - birthDate.getFullYear();
      let months = today.getMonth() - birthDate.getMonth();
      let days   = today.getDate() - birthDate.getDate();

      if (days < 0) {
        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        days += prevMonth.getDate();
        months--;
      }
      if (months < 0) {
        months += 12;
        years--;
      }

      if (years < 0 || years > 150) {
        $("#age").val('');
        return;
      }

      $("#age").val(years + " Years " + months + " Months " + days + " Days");
    });
  });
</script>

<script>
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  if (!file.type.startsWith('image/')) {
    alert('Please select an image file.');
    input.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = (e) => document.getElementById('previewImg').src = e.target.result;
  reader.readAsDataURL(file);
}
</script>

<script>
document.getElementById('patientForm').addEventListener('submit', function (e) {
  const fields = [
    { id: 'mobile_no',     name: 'Mobile No' },
    { id: 'spomobile_no',  name: 'Spouse Mobile No' },
    { id: 'relmobile_no',  name: 'Relative Mobile No' }
  ];

  for (let i = 0; i < fields.length; i++) {
    const el = document.getElementById(fields[i].id);
    if (!el) continue;

    const val = el.value.trim();
    if (val === '') continue;

    if (!/^[0-9]{11}$/.test(val)) {
      alert(fields[i].name + ' must be exactly 11 digit number');
      el.focus();
      e.preventDefault();
      return false;
    }
  }
});
</script>
@endpush