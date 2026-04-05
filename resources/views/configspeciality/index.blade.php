@extends('adminlte::page') {{-- or layouts.app --}}
@section('title', 'Specialities')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Config Speciality</h3>
        <a href="{{ route('configspeciality.create') }}" class="btn btn-primary">
            + Add New
        </a>
    </div>

    <form class="mb-3" method="GET" action="{{ route('configspeciality.index') }}">
        <div class="input-group" style="max-width:420px;">
            <input type="text" name="q" class="form-control" placeholder="Search code/name" value="{{ $q }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:80px;">ID</th>
                        <th style="width:180px;">Code</th>
                        <th>Name</th>
                        <th style="width:170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->code }}</td>
                        <td>{{ $row->name }}</td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-warning" href="{{ route('configspeciality.edit', $row->id) }}">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('configspeciality.destroy', $row->id) }}"
                                  style="display:inline-block"
                                  onsubmit="return confirm('Delete this speciality?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center p-4">No data found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $rows->links() }}
        </div>
    </div>

</div>
@endsection
