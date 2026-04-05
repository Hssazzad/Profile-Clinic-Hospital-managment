@extends('adminlte::page')

@section('title', 'Add Round Note')

@section('content_header')
    <h1>Patient Round Entry</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patient Details</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-user mr-1"></i> Name</strong>
                    <p class="text-muted">{{ $admission->patientname }}</p>
                    <hr>
                    <strong><i class="fas fa-id-badge mr-1"></i> Code</strong>
                    <p class="text-muted">{{ $admission->patientcode }}</p>
                    <hr>
                    <strong><i class="fas fa-hospital mr-1"></i> Ward & Bed</strong>
                    <p class="text-muted">
                        Ward: {{ $admission->ward ?? 'N/A' }}, Bed: {{ $admission->bed_no ?? 'N/A' }}
                    </p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admission.round.index') }}" class="btn btn-default btn-block">Back to List</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Current Vitals & Clinical Note</h3>
                </div>
                <form action="{{ route('admission.round.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="admission_id" value="{{ $admission->id }}">
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Round Date</label>
                                    <input type="date" name="round_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Round Time</label>
                                    <input type="time" name="round_time" class="form-control" value="{{ date('H:i') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Temp (°F)</label>
                                    <input type="text" name="temp" class="form-control" placeholder="98.4">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pulse (bpm)</label>
                                    <input type="text" name="pulse" class="form-control" placeholder="72">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>B.P (mmHg)</label>
                                    <input type="text" name="bp" class="form-control" placeholder="120/80">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>SpO2 (%)</label>
                                    <input type="text" name="spo2" class="form-control" placeholder="98">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Doctor's Advice/Clinical Note</label>
                            <textarea name="doctor_note" class="form-control" rows="5" placeholder="Enter clinical findings or advice here..."></textarea>
                        </div>
                    </div>
                    
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Save Round Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop