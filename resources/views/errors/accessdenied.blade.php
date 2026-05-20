@extends('adminlte::page')

@section('title', 'Access Denied')

@section('content_header')
    <h1 class="text-danger"><i class="fas fa-ban"></i> Access Denied</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body text-center">
            <p class="text-danger">{{ $msg ?? 'You do not have permission to view this page.' }}</p>
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>
@stop
