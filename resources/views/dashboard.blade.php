@extends('adminlte::page')

@section('title', 'Clinic Dashboard')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h4 class="mb-0"><b>Clinic Dashboard</b></h4>
    <small class="text-muted">Date: {{ now()->format('d M Y') }}</small>
</div>
@stop

@section('content')

{{-- =======================
   TOP SUMMARY (Cards)
   ======================= --}}
<div class="row">

    <div class="col-md-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalPatients ?? 0 }}</h3>
                <p>Total Patients</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $todayPatients ?? 0 }}</h3>
                <p>Today Patients</p>
            </div>
            <div class="icon"><i class="fas fa-user-injured"></i></div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalDoctors ?? 0 }}</h3>
                <p>Total Doctors</p>
            </div>
            <div class="icon"><i class="fas fa-user-md"></i></div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                {{-- ✅ payments table uses paid_amount --}}
                <h3>{{ number_format((float)($todayRevenue ?? 0), 2) }} ৳</h3>
                <p>Today Revenue</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill"></i></div>
        </div>
    </div>

</div>

{{-- =======================
   SECOND SUMMARY ROW
   ======================= --}}
<div class="row">

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-calendar-day"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Today Appointments</span>
                <span class="info-box-number">{{ $todayAppointments ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Appointments</span>
                <span class="info-box-number">{{ $pendingAppointments ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-prescription"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Today Prescriptions</span>
                <span class="info-box-number">{{ $todayPrescription ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-heartbeat"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Vitals Taken Today</span>
                <span class="info-box-number">{{ $todayVitals ?? 0 }}</span>
            </div>
        </div>
    </div>

</div>

{{-- =======================
   MAIN GRID: Chart + Doctor List + Today Patients
   ======================= --}}
<div class="row">

    {{-- Patients Chart --}}
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Patients Last 7 Days</h3>
            </div>
            <div class="card-body">
                <canvas id="patientChart" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- Doctor List --}}
    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-md mr-1"></i> Doctor List</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Doctor</th>
                            <th>Speciality</th>
                            <th class="text-right">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse(($doctors ?? []) as $i => $d)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>
                                    <b>{{ $d->name ?? 'N/A' }}</b>
                                    @if(!empty($d->mobile))
                                        <div class="text-muted" style="font-size:12px;">{{ $d->mobile }}</div>
                                    @endif
                                </td>
                                <td>{{ $d->speciality ?? '-' }}</td>
                                <td class="text-right">
                                    @php $st = ($d->status ?? 1); @endphp
                                    @if($st == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted p-3">No doctors found</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Today Patient List --}}
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-injured mr-1"></i> Today Patients (Latest)</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Code</th>
                    <th>Mobile</th>
                    <th class="text-right">Time</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($todayPatientRows ?? []) as $k => $p)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td><b>{{ $p->patientname ?? 'N/A' }}</b></td>
                        <td>{{ $p->patientcode ?? '-' }}</td>
                        <td>{{ $p->mobile_no ?? '-' }}</td>
                        <td class="text-right text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted p-3">No patient visited today</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Expecting $last7days = [{date:'2026-02-14', total: 12}, ...]
    const labels = @json(($last7days ?? collect())->pluck('date'));
    const values = @json(($last7days ?? collect())->pluck('total'));

    const el = document.getElementById('patientChart');
    if (el) {
        new Chart(el, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Patients',
                    data: values,
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@stop