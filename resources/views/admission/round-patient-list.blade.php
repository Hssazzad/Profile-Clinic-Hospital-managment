@extends('adminlte::page')

@section('title', 'Round Patient List')

@section('content_header')
    <h1>Round Patient Management</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-nurse mr-1"></i> Admitted Patients</h3>
                    <div class="card-tools">
                        <form action="{{ route('admission.round.index') }}" method="GET">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="q" class="form-control float-right" placeholder="Search name/code..." value="{{ $q ?? '' }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Code</th>
                                <th>Patient Name</th>
                                <th>Ward & Bed</th>
                                <th>Admit Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $row)
                                <tr>
                                    <td><span class="text-bold text-primary">{{ $row->patientcode }}</span></td>
                                    <td>{{ $row->patientname }}</td>
                                    <td>
                                        <span class="badge badge-secondary"><i class="fas fa-hospital"></i> {{ $row->ward ?? 'N/A' }}</span>
                                        <span class="badge badge-info"><i class="fas fa-bed"></i> {{ $row->bed_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ date('d M, Y', strtotime($row->admit_date)) }}</td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a href="{{ route('admission.round.create', $row->id) }}" class="btn btn-success btn-sm" title="Add Round Note">
                                                <i class="fas fa-notes-medical"></i> Add Note
                                            </a>
                                            <a href="{{ route('admission.round.history', $row->id) }}" class="btn btn-info btn-sm" title="View History">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-user-slash fa-2x mb-2"></i><br>
                                        No admitted patients found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination if exists --}}
                @if(method_exists($patients, 'links'))
                <div class="card-footer clearfix">
                    {{ $patients->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Round patient list loaded.'); </script>
@stop