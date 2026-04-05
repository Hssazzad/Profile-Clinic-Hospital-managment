@extends('adminlte::page')

@section('title', 'Create Template')

@section('content_header')
    <h1>Create Template</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('templates.store') }}">
                @csrf
                
                 <div class="form-group">
                    <label>Template ID</label>
                    <input type="text"
                           name="templateid"
                           class="form-control"
                           value="{{ $nextTemplateId }}"
                           readonly>
                </div>

                <div class="form-group">
                    <label for="title">Template Title <span class="text-danger">*</span></label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title') }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label for="description">Description / Body</label>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Template
                </button>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                    Back to List
                </a>
            </form>
        </div>
    </div>
@stop
