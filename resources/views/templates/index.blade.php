@extends('adminlte::page')

@section('title', 'Template List')

@section('content_header')
    <h1>Template List</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-2">
        <a href="{{ route('templates.create') }}" class="btn btn-success btn-sm">
            + New Template
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                @forelse($templates as $tpl)
                    <tr>
                        <td>{{ $tpl->id }}</td>
                        <td>{{ $tpl->title }}</td>
                        <td>
                            @if($tpl->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $tpl->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No templates found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $templates->links() }}
        </div>
    </div>
@stop
