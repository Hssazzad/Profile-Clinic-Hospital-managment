@extends('adminlte::page')

@section('title', 'Add Patient')

@section('content_header')
    <h1 class="text-primary">Add Patient (রোগী নিবন্ধন)</h1>
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
            <form action="{{ route('patients.store') }}" method="post" id="patientForm">
                @csrf

                <table class="table table-borderless align-middle w-100" style="max-width:900px;">
                    <tbody>
                        <tr>
							<td class="fw-semibold" style="width:220px;">Patient Code (রোগী কোড)</td>
							<td style="width:10px;">:</td>
							<td>
								<input type="text"
									   name="patientcode"
									   class="form-control"
									   value="{{ old('patientcode', $newCode ?? '') }}"
									   readonly>
							</td>
						</tr>


                        <tr>
                            <td class="fw-semibold">Patient Name (নাম)  <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td>
                                <input type="text" name="patientname" class="form-control" required
                                       value="{{ old('patientname') }}">
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Email</td>
                            <td>:</td>
                            <td><input type="email" name="email" class="form-control" value="{{ old('email') }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Address (ঠিকানা)</td>
                            <td>:</td>
                            <td><input type="text" name="address" class="form-control" value="{{ old('address') }}"></td>
                        </tr>                     

                        <tr>
                            <td class="fw-semibold">Date of Birth (জন্মতারিখ)</td>
                            <td>:</td>
                            <td><input type="date" name="date_of_birth" id="dob" class="form-control" value="{{ old('date_of_birth') }}"></td>
                        </tr>
						 <tr>
                            <td class="fw-semibold">Age (বয়স)</td>
                            <td>:</td>
                            <td><input type="number" name="age" id="age" class="form-control" min="0" max="150" value="{{ old('age') }}"></td>
                        </tr>
						
                        <tr>
                            <td class="fw-semibold">Mobile No (মোবাইল)     <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span>
						</td>
                            <td>:</td>
                            <td><input type="text" name="mobile_no" class="form-control" value="{{ old('mobile_no') }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Contact No <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td><input type="text" name="contact_no" class="form-control" value="{{ old('contact_no') }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">NID Number</td>
                            <td>:</td>
                            <td><input type="text" name="nid_number" class="form-control" value="{{ old('nid_number') }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Gender (লিঙ্গ) <span style="color:red; font-size:22px; font-weight:bold; line-height:1;">*</span></td>
                            <td>:</td>
                            <td>
                                <select name="gender" class="form-select select2">
                                    @php $g = old('gender'); @endphp
                                    <option value="">-- Select --</option>
                                    <option value="Male"   {{ $g==='Male'?'selected':'' }}>Male</option>
                                    <option value="Female" {{ $g==='Female'?'selected':'' }}>Female</option>
                                    <option value="Other"  {{ $g==='Other'?'selected':'' }}>Other</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Blood Group (রক্তের গ্রুপ)</td>
                            <td>:</td>
                            <td><input type="text" name="blood_group" class="form-control" placeholder="A+, O-, B+ ..." value="{{ old('blood_group') }}"></td>
                        </tr>

                        {{-- ===================== Surgery / Operation Section ===================== --}}
                        <tr class="table-light">
                            <td colspan="3" class="fw-bold">Surgery / Operation (অপারেশন তথ্য)</td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Operation Name (অপারেশনের নাম) <span class="text-danger">*</span></td>
                            <td>:</td>
                            <td>
                                <select name="operationname" class="form-select select2" >
                                    <option value="">-- Select Operation --</option>
                                    @foreach(($operations ?? []) as $id => $name)
                                        <option value="{{ $id }}" {{ old('operationname') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Operation Date (তারিখ) <span class="text-danger">*</span></td>
                            <td>:</td>
                            <td><input type="date" name="operationdate" class="form-control"  value="{{ old('operationdate') }}"></td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Operation Time (সময়) <span class="text-danger">*</span></td>
                            <td>:</td>
                            <td><input type="time" name="operationtime" class="form-control"  value="{{ old('operationtime') }}"></td>
                        </tr>

                        {{-- Surgeon dropdowns --}}
                        <tr>
                            <td class="fw-semibold">Surgeon Name (সার্জন)</td>
                            <td>:</td>
                            <td>
                                <select name="sergioncode" class="form-select select2">
                                    <option value="">-- Select Surgeon --</option>
                                    @foreach(($surgeons ?? []) as $id => $name)
                                        <option value="{{ $id }}" {{ old('sergioncode') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Assistant Surgeon (সহকারী সার্জন)</td>
                            <td>:</td>
                            <td>
                                <select name="asstsurgeon" class="form-select select2">
                                    <option value="">-- Select Assistant Surgeon --</option>
                                    @foreach(($assistants ?? []) as $id => $name)
                                        <option value="{{ $id }}" {{ old('asstsurgeon') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Anesthesia Doctor (অ্যানেসথেশিয়া ডাক্তার)</td>
                            <td>:</td>
                            <td>
                                <select name="anestiadoctor" class="form-select select2">
                                    <option value="">-- Select Anesthesia Doctor --</option>
                                    @foreach(($anesthetics ?? []) as $id => $name)
                                        <option value="{{ $id }}" {{ old('anestiadoctor') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">Operation Note (নোট)</td>
                            <td>:</td>
                            <td><textarea name="operationnote" rows="3" class="form-control">{{ old('operationnote') }}</textarea></td>
                        </tr>

                        {{-- ===================== End Surgery / Operation Section ===================== --}}
                        <tr>
                            <td class="fw-semibold">Notes (অন্যান্য)</td>
                            <td>:</td>
                            <td><textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3">
                    <button class="btn btn-primary">Save</button>
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
$(function() {
    // Activate Select2 on all dropdowns
    $('.select2').select2({
       
        width: '100%',
       
       
    });

    // Auto-calc age from DOB
    $('#dob').on('change', function() {
        const dob = this.value ? new Date(this.value) : null;
        const ageInput = $('#age');
        if (!dob) return;
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        ageInput.val(age >= 0 && age <= 150 ? age : '');
    });
});
</script>
@endpush
