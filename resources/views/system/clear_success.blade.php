@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">? Success</h3>
        </div>
        <div class="card-body">
            <p>{{ $message }}</p>
            <ul>
                @foreach($commands as $cmd)
                    <li><code>php artisan {{ $cmd }}</code></li>
                @endforeach
            </ul>
        </div>
        <div class="card-footer">
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
@stop
