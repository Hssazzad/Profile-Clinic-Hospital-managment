@extends('adminlte::page')

@section('title', 'Add Medicine')

@section('content')
<div class="container-fluid">

    {{-- Success / Error Alerts --}}
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


    {{-- Main Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Common Medicine Setup</h5>

            <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#addMedicineModal">
                <i class="fas fa-plus"></i> Add Medicine
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th style="width:60px;">#</th>
                            <th style="width:90px;">Code</th>
                            <th>Name</th>
                            <th style="width:170px;">Group Name</th>
                            <th style="width:120px;">Strength</th>
                            <th style="width:90px;">Active</th>
                            <th style="width:170px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($medicines as $i => $row)
                            <tr>
                                <td class="text-center">{{ $medicines->firstItem() + $i }}</td>
                                <td class="text-center">{{ $row->code }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->GroupName }}</td>
                                <td class="text-center">{{ $row->strength }}</td>
                                <td class="text-center">
                                    @if($row->active==1)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td class="text-center">

                                    {{-- Edit Button --}}
                                    <button class="btn btn-primary btn-sm"
                                            data-toggle="modal"
                                            data-target="#editMedicineModal{{ $row->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('settings.medicines.destroy', $row->id) }}"
                                          method="POST" style="display:inline-block;"
                                          onsubmit="return confirm('Delete this medicine?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>


                            {{-- EDIT MODAL --}}
                            <div class="modal fade" id="editMedicineModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form method="POST" action="{{ route('settings.medicines.update', $row->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Edit Medicine</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <label>Code</label>
                                                        <input type="number" name="code"
                                                               class="form-control"
                                                               value="{{ $row->code }}">
                                                    </div>

                                                    <div class="col-md-9">
                                                        <label>Name</label>
                                                        <input type="text" name="name"
                                                               class="form-control"
                                                               value="{{ $row->name }}">
                                                    </div>

                                                    <div class="col-md-6 mt-2">
                                                        <label>Group Name *</label>
                                                        <input type="text" name="GroupName"
                                                               class="form-control"
                                                               value="{{ $row->GroupName }}" required>
                                                    </div>

                                                    <div class="col-md-3 mt-2">
                                                        <label>Strength *</label>
                                                        <input type="text" name="strength"
                                                               class="form-control"
                                                               value="{{ $row->strength }}" required>
                                                    </div>

                                                    <div class="col-md-3 mt-2">
                                                        <label>Active</label>
                                                        <select name="active" class="form-control">
                                                            <option value="1" {{ $row->active==1?'selected':'' }}>Yes</option>
                                                            <option value="0" {{ $row->active==0?'selected':'' }}>No</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-primary">Update</button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-4">No medicine found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $medicines->links() }}
        </div>

    </div>
</div>



{{-- ADD MODAL --}}
<div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('settings.medicines.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Add Medicine</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-3">
                            <label>Code</label>
                            <input type="number" name="code" class="form-control">
                        </div>

                        <div class="col-md-9">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2">
                            <label>Group Name *</label>
                            <input type="text" name="GroupName" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Strength *</label>
                            <input type="text" name="strength" class="form-control" required>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Active</label>
                            <select name="active" class="form-control">
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-info">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
