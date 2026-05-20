Admit Medicine
@if($medicines->count() > 0)

<table class="table table-bordered table-striped table-sm">
    <thead class="bg-primary text-white">
        <tr>
            <th>#</th>
            <th>Medicine</th>
            <th>Strength</th>
            <th>Dose</th>
            <th>Morning</th>
            <th>Noon</th>
            <th>Night</th>
            <th width="120">Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($medicines as $index => $m)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $m->name }}</td>
            <td>{{ $m->strength }}</td>
            <td>{{ $m->dose }}</td>
            <td>{{ $m->morning }}</td>
            <td>{{ $m->noon }}</td>
            <td>{{ $m->night }}</td>
            <td>
                <button type="button"
                    class="btn btn-warning btn-xs editMedicineBtn"
                    data-id="{{ $m->id }}"
                    data-name="{{ $m->name }}"
                    data-strength="{{ $m->strength }}"
                    data-dose="{{ $m->dose }}"
                    data-morning="{{ $m->morning }}"
                    data-noon="{{ $m->noon }}"
                    data-night="{{ $m->night }}"
                    data-templeteid="{{ $m->templeteid }}"
                    data-order_type="{{ $m->order_type }}">
                    Edit
                </button>

                <button type="button"
                    class="btn btn-danger btn-xs deleteMedicineBtn"
                    data-id="{{ $m->id }}">
                    Delete
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@else
<div class="alert alert-warning">
    No medicine found for this patient.
</div>
@endif