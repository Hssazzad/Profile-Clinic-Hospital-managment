@extends('adminlte::page')

@section('title', 'Pre-Surgery Check')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Pre-Surgery Assessment</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Patient Pre-Surgery Checklist</h3>
        </div>
        <div class="card-body">
            <p>????? ????? ????-???????? ???? ?? ???? ??????? ??????</p>
            
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Task</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Consent Form Signed</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop