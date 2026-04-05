@extends('adminlte::page')

@section('title', 'Edit Patient')

@section('content_header')
    <h1 class="text-primary">Edit Patient (রোগী সম্পাদনা)</h1>
@stop

@section('content')
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
            <form action="{{ route('patients.update', $patient->id) }}" method="post" id="patientForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <table class="table table-borderless align-middle w-100" style="max-width:900px;">
                    <tbody>
                        <tr>
							<td class="fw-semibold" style="width:220px;">Patient Photo (রোগীর ছবি)</td>
							<td style="width:10px;">:</td>
							<td>
								<img id="previewImg"
                                     src="{{ $patient->photo ? asset('uploads/photos/' . $patient->photo) : asset('uploads/photos/no_image.png') }}"
                                     style="width:150px;height:150px;border:1px solid #ccc;object-fit:cover;margin:8px 0;">
                                <br>

                                <input type="file"
                                       name="photo"
                                       id="photoInput"
                                       accept="image/*"
                                       capture="environment"
                                       onchange="previewImage(this)"
                                       style="display:none;">

                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photoInput').click()">
                                        📁 Choose File
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="openCameraOptions()">
                                        📷 Take Photo
                                    </button>
                                    <input type="file" 
                                           id="cameraInput" 
                                           accept="image/*" 
                                           capture="environment" 
                                           style="display:none;"
                                           onchange="handleCameraPhoto(this)">
                                    <input type="file" 
                                           id="cameraInputUser" 
                                           accept="image/*" 
                                           capture="user" 
                                           style="display:none;"
                                           onchange="handleCameraPhoto(this)">
                                </div>

                                @error('photo')
                                    <div style="color:red;margin-top:6px;">{{ $message }}</div>
                                @enderror
                               
							</td>
						</tr>
                        <tr>
							<td class="fw-semibold" style="width:220px;">Patient Code (রোগী কোড)</td>
							<td style="width:10px;">:</td>
							<td>
								<input type="text"
									   name="patientcode"
									   class="form-control"
									   value="{{ $patient->patientcode }}"
									   readonly>
							</td>
						</tr>
						
                        <tr>
                            <td class="fw-semibold">Patient Name (নাম)  <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td>
                                <input type="text" name="patientname" class="form-control" required
                                       value="{{ $patient->patientname }}">
                            </td>
                        </tr>
						<tr>
                            <td class="fw-semibold">Patient father Name ( পিতার নাম)  <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td>
                                <input type="text" name="patientfather" class="form-control" required
                                       value="{{ $patient->patientfather }}">
                            </td>
                        </tr>
						
                       <tr>
                            <td class="fw-semibold">Own Mobile  ( নিজের মোবাইল)     <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span>
						</td>
                            <td>:</td>
                            <td><input type="text" name="mobile_no" class="form-control" value="{{ $patient->mobile_no }}"></td>
                        </tr>
						<tr>
                            <td class="fw-semibold">Spouse Mobile  ( স্বামী বা স্ত্রীর মোবাইল)     
						</td>
                            <td>:</td>
                            <td><input type="text" name="spomobile_no" class="form-control" value="{{ $patient->spomobile_no }}"></td>
                        </tr>
						<tr>
                            <td class="fw-semibold">Relative Mobile  (আত্মীয়ের মোবাইল)     
						</td>
                            <td>:</td>
                            <td><input type="text" name="relmobile_no" class="form-control" value="{{ $patient->relmobile_no }}"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Email</td>
                            <td>:</td>
                            <td><input type="email" name="email" class="form-control" value="{{ $patient->email }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Date of Birth (জন্মতারিখ)<span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td><input type="date" name="date_of_birth" id="dob" class="form-control" value="{{ $patient->date_of_birth }}"></td>
                        </tr>
						 <tr>
                            <td class="fw-semibold">Age (বয়স)  <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td><input type="text" name="age" id="age" class="form-control" min="0" max="150" value="{{ $patient->age }}" required></td>
                        </tr>		

                        <tr>
                            <td class="fw-semibold">NID Number</td>
                            <td>:</td>
                            <td><input type="text" name="nid_number" class="form-control" value="{{ $patient->nid_number }}"></td>
                        </tr>

                        <tr>
							<td class="fw-semibold">
								Gender (লিঙ্গ)
								<span class="text-danger fw-bold" style="font-size:22px; line-height:1;">*</span>
							</td>
							<td>:</td>
							<td>
								<select name="gender" class="form-select select2" required>
									<option value="">-- Select --</option>
									<option value="Male"   {{ $patient->gender === 'Male' ? 'selected' : '' }}>Male</option>
									<option value="Female" {{ $patient->gender === 'Female' ? 'selected' : '' }}>Female</option>
									<option value="Other"  {{ $patient->gender === 'Other' ? 'selected' : '' }}>Other</option>
								</select>
							</td>
						</tr>

                        <tr>
                            <td class="fw-semibold">Blood Group (রক্তের গ্রুপ)</td>
                            <td>:</td>
                            <td>
							<select name="blood_group" class="form-control select2" required>
								<option value="">-- Select Blood Group --</option>
								<option value="A+" {{ $patient->blood_group === 'A+' ? 'selected' : '' }}>A+</option>
								<option value="A-" {{ $patient->blood_group === 'A-' ? 'selected' : '' }}>A-</option>
								<option value="B+" {{ $patient->blood_group === 'B+' ? 'selected' : '' }}>B+</option>
								<option value="B-" {{ $patient->blood_group === 'B-' ? 'selected' : '' }}>B-</option>
								<option value="AB+" {{ $patient->blood_group === 'AB+' ? 'selected' : '' }}>AB+</option>
								<option value="AB-" {{ $patient->blood_group === 'AB-' ? 'selected' : '' }}>AB-</option>
								<option value="O+" {{ $patient->blood_group === 'O+' ? 'selected' : '' }}>O+</option>
								<option value="O-" {{ $patient->blood_group === 'O-' ? 'selected' : '' }}>O-</option>
							</select>

							</td>
                        </tr>
                        <tr>
							  <td class="fw-semibold">District (জেলা)</td>
							  <td>:</td>
							  <td class="d-flex gap-2 align-items-center">
								<select id="district" name="district" class="form-control select2" required>
								  <option value="">-- Select District --</option>
								  @foreach($districts as $d)
									<option value="{{ $d->code }}" {{ $patient->district === $d->code ? 'selected' : '' }}>{{ $d->name }}</option>
								  @endforeach
								</select>
							  </td>
							</tr>

							<tr>
							  <td class="fw-semibold">Upozila</td><td>:</td>
							  <td>
								<select id="upozila" name="upozila" class="form-control select2">
								  <option value="">-- Select Upozila --</option>
								</select>
							  </td>
							</tr>

							<tr>
							  <td class="fw-semibold">Union</td><td>:</td>
							  <td>
								<select id="union" name="union" class="form-control select2">
								  <option value="">-- Select Union --</option>
								</select>
							  </td>
							</tr>

							<tr>
							  <td class="fw-semibold">Village</td><td>:</td>
							  <td>
								<select id="village" name="village" class="form-control select2">
								  <option value="">-- Select Village --</option>
								</select>
							  </td>
							</tr>

                        <tr>
                            <td class="fw-semibold">Notes (অন্যান্য)</td>
                            <td>:</td>
                            <td><textarea name="notes" rows="3" class="form-control">{{ $patient->notes }}</textarea></td>
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
{{-- Select2 CDN --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Initialize Select2 Elements
    $(".select2").select2();

    // Load existing data
    @if($patient->district)
    loadUpozila('{{ $patient->district }}', '{{ $patient->upozila ?? '' }}');
    @endif

    @if($patient->upozila)
    loadUnion('{{ $patient->upozila }}', '{{ $patient->union ?? '' }}');
    @endif

    @if($patient->union)
    loadVillage('{{ $patient->union }}', '{{ $patient->village ?? '' }}');
    @endif

    // District → Upozila
    $("#district").on("change", function () {
        var district = $(this).val();
        $("#upozila").html('<option value="">Loading...</option>').prop('disabled', true);
        $("#union").html('<option value="">-- Select Union --</option>').prop('disabled', true);
        $("#village").html('<option value="">-- Select Village --</option>').prop('disabled', true);

        if (!district) {
            $("#upozila").html('<option value="">-- Select Upozila --</option>').prop('disabled', true);
            return;
        }

        loadUpozila(district);
    });

    function loadUpozila(district, selectedId = '') {
        $.ajax({
            type: "POST",
            url: "{{ route('api.fetch_upozila') }}",
            data: {
                _token: "{{ csrf_token() }}",
                district: district
            },
            success: function (data) {
                $("#upozila").html(data).prop('disabled', false);
                if (selectedId) {
                    $("#upozila").val(selectedId).trigger('change');
                }
            },
            error: function () {
                $("#upozila").html('<option value="">Failed to load</option>').prop('disabled', true);
            }
        });
    }

    // Upozila → Union
    $("#upozila").on("change", function () {
        var upozila = $(this).val();
        $("#union").html('<option value="">Loading...</option>').prop('disabled', true);
        $("#village").html('<option value="">-- Select Village --</option>').prop('disabled', true);

        if (!upozila) {
            $("#union").html('<option value="">-- Select union --</option>').prop('disabled', true);
            return;
        }

        loadUnion(upozila);
    });

    function loadUnion(upozila, selectedId = '') {
        $.ajax({
            type: "POST",
            url: "{{ route('api.fetch_union') }}",
            data: {
                _token: "{{ csrf_token() }}",
                upozila: upozila
            },
            success: function (data) {
                $("#union").html(data).prop('disabled', false);
                if (selectedId) {
                    $("#union").val(selectedId).trigger('change');
                }
            },
            error: function () {
                $("#union").html('<option value="">Failed to load</option>').prop('disabled', true);
            }
        });
    }

    // Union → Village
    $("#union").on("change", function () {
        var union = $(this).val();
        $("#village").html('<option value="">Loading...</option>').prop('disabled', true);

        if (!union) {
            $("#village").html('<option value="">-- Select Village --</option>').prop('disabled', true);
            return;
        }

        loadVillage(union);
    });

    function loadVillage(union, selectedId = '') {
        $.ajax({
            type: "POST",
            url: "{{ route('api.fetch_village') }}",
            data: {
                _token: "{{ csrf_token() }}",
                union: union
            },
            success: function (data) {
                $("#village").html(data).prop('disabled', false);
                if (selectedId) {
                    $("#village").val(selectedId).trigger('change');
                }
            },
            error: function () {
                $("#village").html('<option value="">Failed to load</option>').prop('disabled', true);
            }
        });
    }

    // DOB to Age calculation
    $("#dob").on("change", function () {
        var dob = $(this).val();
        if (!dob) return;

        var birthDate = new Date(dob);
        var today = new Date();

        var years  = today.getFullYear() - birthDate.getFullYear();
        var months = today.getMonth() - birthDate.getMonth();
        var days   = today.getDate() - birthDate.getDate();

        if (days < 0) {
            var prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
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

        var ageText = years + " Years " + months + " Months " + days + " Days";
        $("#age").val(ageText);
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

function openCameraOptions() {
    const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        const rearCamera = document.getElementById('cameraInput');
        const frontCamera = document.getElementById('cameraInputUser');
        
        rearCamera.click();
        
        setTimeout(() => {
            if (!rearCamera.files || rearCamera.files.length === 0) {
                frontCamera.click();
            }
        }, 1000);
    } else {
        showCameraDialog();
    }
}

function showCameraDialog() {
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 9999;
        text-align: center;
    `;
    
    dialog.innerHTML = `
        <h5 style="margin-bottom: 15px;">Camera Options</h5>
        <button onclick="tryCamera('environment')" style="margin: 5px; padding: 8px 15px;" class="btn btn-primary">📷 Rear Camera</button>
        <button onclick="tryCamera('user')" style="margin: 5px; padding: 8px 15px;" class="btn btn-info">👤 Front Camera</button>
        <button onclick="tryCamera('')" style="margin: 5px; padding: 8px 15px;" class="btn btn-secondary">📸 Any Camera</button>
        <button onclick="closeCameraDialog()" style="margin: 5px; padding: 8px 15px;" class="btn btn-outline-secondary">Cancel</button>
    `;
    
    document.body.appendChild(dialog);
    window.currentCameraDialog = dialog;
}

function tryCamera(captureType) {
    closeCameraDialog();
    
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    if (captureType) {
        input.capture = captureType;
    }
    input.onchange = function(e) {
        if (e.target.files && e.target.files[0]) {
            handleCameraPhoto(e.target);
        }
    };
    input.click();
}

function closeCameraDialog() {
    if (window.currentCameraDialog) {
        document.body.removeChild(window.currentCameraDialog);
        window.currentCameraDialog = null;
    }
}

function handleCameraPhoto(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    
    document.getElementById('photoInput').files = dataTransfer.files;
    
    previewImage(document.getElementById('photoInput'));
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
