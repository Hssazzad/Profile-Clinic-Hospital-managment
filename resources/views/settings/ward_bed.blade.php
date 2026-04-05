@extends('adminlte::page')

@section('title', 'Ward & Bed Management')

@section('content_header')
    <h1>Ward & Bed Management</h1>
@stop

@section('content')
    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        {{-- Left Side: Add New Ward/Bed Form --}}
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add New Ward/Bed</h3>
                </div>
                <form action="{{ route('settings.wardbed.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="ward_name">Ward Name</label>
                            <input type="text" name="ward_name" class="form-control" placeholder="e.g. Female Ward" required>
                        </div>
                        <div class="form-group">
                            <label for="bed_no">Bed No.</label>
                            <input type="text" name="bed_no" class="form-control" placeholder="e.g. B-101">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">Save Data</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Side: List of Ward/Beds --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Existing Wards & Beds</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ward Name</th>
                                <th>Bed No.</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wards as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->ward_name }}</td>
                                    <td>{{ $row->bed_no }}</td>
                                    <td>
                                        <span class="badge {{ $row->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $row->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('settings.wardbed.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $wards->links() }}
                </div>
            </div>
        </div>
    </div>
@stop