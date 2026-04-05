@extends('adminlte::page')
@section('title', 'Create Speciality')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><b>Create Speciality</b></div>
        <div class="card-body">

            <form method="POST" action="{{ route('configspeciality.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Code *</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code') }}" maxlength="25" required>
                    @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name') }}" maxlength="50" required>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Save</button>
                <a href="{{ route('configspeciality.index') }}" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>
@endsection
