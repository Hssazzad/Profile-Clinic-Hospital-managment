@extends('adminlte::page')

@section('title', 'Patient Round History')

@section('content_header')
    <h1>Round History</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-user mr-1"></i> Patient:</strong>
                            <span class="text-muted">{{ $admission->patientname }} ({{ $admission->patientcode }})</span>
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-hospital mr-1"></i> Ward/Bed:</strong>
                            <span class="text-muted">{{ $admission->ward ?? 'N/A' }} / {{ $admission->bed_no ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-alt mr-1"></i> Admit Date:</strong>
                            <span class="text-muted">{{ date('d M, Y', strtotime($admission->admit_date)) }}</span>
                        </div>
                        <div class="col-md-3 text-right">
                            <a href="{{ route('admission.round.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('admission.round.create', $admission->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> New Round
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Clinical Progress Notes</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-navy">
                                <tr>
                                    <th style="width: 150px;">Date & Time</th>
                                    <th style="width: 100px;">Temp (°F)</th>
                                    <th style="width: 100px;">Pulse</th>
                                    <th style="width: 100px;">B.P</th>
                                    <th style="width: 100px;">SpO2</th>
                                    <th>Doctor's Clinical Note / Advice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $round)
                                    <tr>
                                        <td>
                                            <span class="text-bold">{{ date('d-m-Y', strtotime($round->round_date)) }}</span><br>
                                            <small class="text-muted">{{ date('h:i A', strtotime($round->round_time)) }}</small>
                                        </td>
                                        <td>{{ $round->temp ?? '-' }}</td>
                                        <td>{{ $round->pulse ?? '-' }}</td>
                                        <td>{{ $round->bp ?? '-' }}</td>
                                        <td>{{ $round->spo2 ?? '-' }}%</td>
                                        <td class="text-justify">
                                            {!! nl2br(e($round->doctor_note)) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-2"></i><br>
                                            No round notes found for this patient yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-navy { background-color: #001f3f !important; color: white; }
    .table td { vertical-align: middle !important; }
</style>
@stop