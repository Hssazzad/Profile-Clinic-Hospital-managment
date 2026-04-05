@extends('adminlte::page')

@section('title', 'Add Parent Menu')

@section('content_header')
    <h1 class="text-primary">Add Parent Menu</h1>
@stop

@section('content')
<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- ✅ Success Message --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ Validation Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('Parentstore') }}" method="POST">
                @csrf

                {{-- Parent Code --}}
                <div class="form-group mb-3">
                    <label for="parentcode">Parent Code</label>
                    <input type="number" class="form-control @error('parentcode') is-invalid @enderror"
                           id="parentcode" name="parentcode"
                           value="{{ old('parentcode') }}" placeholder="Enter Parent Code" required>
                    @error('parentcode')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Parent Name --}}
                <div class="form-group mb-3">
                    <label for="parentname">Parent Name</label>
                    <input type="text" class="form-control @error('parentname') is-invalid @enderror"
                           id="parentname" name="parentname"
                           value="{{ old('parentname') }}" placeholder="Enter Parent Name" required>
                    @error('parentname')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Controller Name --}}
                <div class="form-group mb-3">
                    <label for="controllername">Controller Name</label>
                    <input type="text" class="form-control @error('controllername') is-invalid @enderror"
                           id="controllername" name="controllername"
                           value="{{ old('controllername') }}" placeholder="Enter Controller Name" required>
                    @error('controllername')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Main Route Name --}}
                <div class="form-group mb-3">
                    <label for="mainroutename">Main Route Name</label>
                    <input type="text" class="form-control @error('mainroutename') is-invalid @enderror"
                           id="mainroutename" name="mainroutename"
                           value="{{ old('mainroutename') }}" placeholder="Enter Main Route Name" required>
                    @error('mainroutename')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
            </form>
        </div>
    </div>
</div>
@stop
