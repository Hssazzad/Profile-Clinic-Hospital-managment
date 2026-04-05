@extends('adminlte::page')
@section('title', 'Edit Speciality')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><b>Edit Speciality</b></div>
        <div class="card-body">

            <form method="POST" action="{{ route('configspeciality.update', $row->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Code *</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code', $row->code) }}" maxlength="25" required>
                    @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $row->name) }}" maxlength="50" required>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('configspeciality.index') }}" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>
@endsection
