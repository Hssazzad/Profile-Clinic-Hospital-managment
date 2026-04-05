@extends('adminlte::page')

@section('title', 'Surgery Templates')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Surgery Templates</h1>
        <div>
            <a href="{{ route('surgery-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Template
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td>{{ $template->template_name }}</td>
                                <td>{{ $template->creator?->name ?? 'System' }}</td>
                                <td>{{ $template->created_at->format('d-M-Y H:i') }}</td>
                                <td>
                                    @if ($template->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('surgery-templates.edit', $template) }}" class="btn btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('surgery-templates.print', $template) }}" class="btn btn-success" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger delete-template" data-id="{{ $template->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No surgery templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $templates->links() }}
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this surgery template? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    $('.delete-template').click(function() {
        var templateId = $(this).data('id');
        $('#deleteForm').attr('action', '/surgery-templates/' + templateId);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
