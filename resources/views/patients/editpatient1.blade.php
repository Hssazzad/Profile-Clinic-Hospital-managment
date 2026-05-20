@extends('adminlte::page')

@section('title', 'Edit Patient')

@section('content_header')
  <h1 class="text-primary">Edit Patient (রোগী তথ্য সংশোধন)</h1>
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
      <form action="{{ route('patients.updatepatient', ['id' => $patient->id]) }}"
            method="post"
            id="patientForm"
            enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <table class="table table-borderless align-middle w-100" style="max-width:900px;">
          <tbody>

            {{-- Photo --}}
            <tr>
              <td class="fw-semibold" style="width:220px;">Photo</td>
              <td style="width:10px;">:</td>
              <td>
                <img id="previewImg"
                     src="{{ !empty($patient->photo) ? asset('uploads/photos/' . $patient->photo) : asset('uploads/photos/no_image.png') }}"
                     style="width:150px;height:150px;border:1px solid #ccc;object-fit:cover;margin:8px 0;">
                <br>

                <input type="file"
                       name="photo"
                       id="photoInput"
                       accept="image/*"
                       onchange="previewImage(this)">

                <button type="button" class="btn btn-info btn-sm mt-2" onclick="openCamera()">📷 Open Camera</button>

                <div id="cameraBox" style="display:none; margin-top:10px;">
                  <video id="camera" width="200" height="150" autoplay playsinline style="border:1px solid #ccc;"></video><br>

                  <button type="button" class="btn btn-success btn-sm mt-2" onclick="capturePhoto()">Capture</button>
                  <button type="button" class="btn btn-danger btn-sm mt-2" onclick="closeCamera()">Close</button>

                  <canvas id="canvas" width="200" height="150" style="display:none;"></canvas>
                </div>

                <input type="hidden" name="camera_image" id="camera_image">

                @error('photo')
                  <div style="color:red;margin-top:6px;">{{ $message }}</div>
                @enderror
              </td>
            </tr>

            {{-- Patient code --}}
            <tr>
              <td class="fw-semibold" style="width:220px;">Patient Code (রোগী কোড)</td>
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
              <td class="fw-semibold">Patient Name (নাম) <span style="color:red; font-size:22px; font-weight:bold;">*</span></td>
              <td>:</td>
              <td>
                <input type="text" name="patientname" class="form-control @error('patientname') is-invalid @enderror" required value="{{ old('patientname', $patient->patientname) }}">
                @error('patientname') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Father --}}
            <tr>
              <td class="fw-semibold">Patient father Name (পিতার নাম)</td>
              <td>:</td>
              <td>
                <input type="text" name="patientfather" class="form-control @error('patientfather') is-invalid @enderror" value="{{ old('patientfather', $patient->patientfather) }}">
                @error('patientfather') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Husband --}}
            <tr>
              <td class="fw-semibold">Husband Name (স্বামীর নাম)</td>
              <td>:</td>
              <td>
                <input type="text" name="patienthusband" class="form-control @error('patienthusband') is-invalid @enderror" value="{{ old('patienthusband', $patient->patienthusband) }}">
                @error('patienthusband') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Own Mobile --}}
            <tr>
              <td class="fw-semibold">Own Mobile (নিজের মোবাইল) <span style="color:red; font-size:22px; font-weight:bold;">*</span></td>
              <td>:</td>
              <td>
                <input type="text"
                       id="mobile_no"
                       name="mobile_no"
                       class="form-control @error('mobile_no') is-invalid @enderror"
                       value="{{ old('mobile_no', $patient->mobile_no) }}"
                       pattern="01[0-9]{9}"
                       maxlength="11"
                       minlength="11"
                       placeholder="01XXXXXXXXX"
                       required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                @error('mobile_no')
                  <div style="color:red;margin-top:6px;">{{ $message }}</div>
                @enderror
              </td>
            </tr>

            {{-- Spouse Mobile --}}
            <tr>
              <td class="fw-semibold">Spouse Mobile (স্বামী/স্ত্রীর মোবাইল)</td>
              <td>:</td>
              <td>
                <input type="text"
                       id="spomobile_no"
                       name="spomobile_no"
                       class="form-control @error('spomobile_no') is-invalid @enderror"
                       value="{{ old('spomobile_no', $patient->spomobile_no) }}"
                       pattern="01[0-9]{9}"
                       maxlength="11"
                       minlength="11"
                       placeholder="01XXXXXXXXX"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                @error('spomobile_no') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Relative Mobile --}}
            <tr>
              <td class="fw-semibold">Relative Mobile (আত্মীয়ের মোবাইল)</td>
              <td>:</td>
              <td>
                <input type="text"
                       id="relmobile_no"
                       name="relmobile_no"
                       class="form-control @error('relmobile_no') is-invalid @enderror"
                       value="{{ old('relmobile_no', $patient->relmobile_no) }}"
                       pattern="01[0-9]{9}"
                       maxlength="11"
                       minlength="11"
                       placeholder="01XXXXXXXXX"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                @error('relmobile_no')
                  <div style="color:red;margin-top:6px;">{{ $message }}</div>
                @enderror
              </td>
            </tr>

            {{-- Email --}}
            <tr>
              <td class="fw-semibold">Email</td>
              <td>:</td>
              <td>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $patient->email) }}">
                @error('email') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- DOB --}}
            <tr>
              <td class="fw-semibold">Date of Birth (জন্মতারিখ)</td>
              <td>:</td>
              <td>
                <input type="date" name="date_of_birth" id="dob" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '') }}">
                @error('date_of_birth') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Age --}}
            <tr>
              <td class="fw-semibold">Age (বয়স) <span style="color:red; font-size:22px; font-weight:bold;">*</span></td>
              <td>:</td>
              <td>
                <input type="text" name="age" id="age" class="form-control @error('age') is-invalid @enderror" required value="{{ old('age', $patient->age) }}">
                @error('age') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- NID --}}
            <tr>
              <td class="fw-semibold">NID Number</td>
              <td>:</td>
              <td>
                <input type="text" name="nid_number" class="form-control @error('nid_number') is-invalid @enderror" value="{{ old('nid_number', $patient->nid_number) }}">
                @error('nid_number') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Gender --}}
            <tr>
              <td class="fw-semibold">Gender (লিঙ্গ) <span class="text-danger fw-bold" style="font-size:22px;">*</span></td>
              <td>:</td>
              <td>
                @php($g = old('gender', $patient->gender))
                <select name="gender" class="form-select select2 @error('gender') is-invalid @enderror" required>
                  <option value="">-- Select --</option>
                  <option value="Male" {{ $g === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ $g === 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ $g === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Blood Group --}}
            <tr>
              <td class="fw-semibold">Blood Group (রক্তের গ্রুপ)</td>
              <td>:</td>
              <td>
                <select name="blood_group" class="form-control select2 @error('blood_group') is-invalid @enderror">
                  <option value="">-- Select Blood Group --</option>
                  @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                  @endforeach
                </select>
                @error('blood_group') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

            {{-- Notes --}}
            <tr>
              <td class="fw-semibold">Notes (অন্যান্য)</td>
              <td>:</td>
              <td>
                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $patient->notes) }}</textarea>
                @error('notes') <div style="color:red;margin-top:6px;">{{ $message }}</div> @enderror
              </td>
            </tr>

          </tbody>
        </table>

        <div class="mt-3">
          <button class="btn btn-primary">Update</button>
          <a href="{{ route('patients.searchpatient') }}" class="btn btn-outline-secondary">Cancel</a>
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
$(function () {
    $('.select2').select2();

    $('#dob').on('change', function () {
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
            $('#age').val('');
            return;
        }

        $('#age').val(years + ' Years ' + months + ' Months ' + days + ' Days');
    });
});

function previewImage(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('previewImg').src = e.target.result;

        // manual file selected হলে camera image clear
        document.getElementById('camera_image').value = '';
    };
    reader.readAsDataURL(file);
}

let stream = null;

function openCamera() {
    document.getElementById('cameraBox').style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (s) {
            stream = s;
            document.getElementById('camera').srcObject = stream;
        })
        .catch(function () {
            alert('Camera access denied or not available.');
        });
}

function capturePhoto() {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/png');

    document.getElementById('previewImg').src = imageData;
    document.getElementById('camera_image').value = imageData;

    // camera capture হলে file input clear
    document.getElementById('photoInput').value = '';

    closeCamera();
}

function closeCamera() {
    document.getElementById('cameraBox').style.display = 'none';

    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

document.getElementById('patientForm').addEventListener('submit', function (e) {
    const fields = [
        { id: 'mobile_no', name: 'Mobile No', required: true },
        { id: 'spomobile_no', name: 'Spouse Mobile No', required: false },
        { id: 'relmobile_no', name: 'Relative Mobile No', required: false }
    ];

    for (let i = 0; i < fields.length; i++) {
        const el = document.getElementById(fields[i].id);
        if (!el) continue;

        const val = el.value.trim();

        if (val === '' && !fields[i].required) continue;

        if (val !== '' && !/^01[0-9]{9}$/.test(val)) {
            alert(fields[i].name + ' must start with 01 and be exactly 11 digits');
            el.focus();
            e.preventDefault();
            return false;
        }
    }
});
</script>

<script>
function formatDOB(input) {
    let value = input.value.replace(/\D/g, ''); // only numbers

    if (value.length > 8) value = value.substring(0, 8);

    let formatted = '';

    if (value.length > 0) {
        formatted = value.substring(0, 2);
    }
    if (value.length >= 3) {
        formatted += '-' + value.substring(2, 4);
    }
    if (value.length >= 5) {
        formatted += '-' + value.substring(4, 8);
    }

    input.value = formatted;
}
</script>
@endpush