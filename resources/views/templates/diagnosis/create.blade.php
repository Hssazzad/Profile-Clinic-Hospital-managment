@extends('adminlte::page')

@section('title', 'Diagnosis Template Builder')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">
    <div class="row">
        {{-- Input Section --}}
        <div class="col-md-12">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa- stethoscope mr-1"></i> Add Diagnosis to Template</h3>
                </div>
                <div class="card-body">
                    <div id="msgBox"></div>

                    <div class="row">
                        {{-- Template Selection --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">Select Template <span class="text-danger">*</span></label>
                                <select id="templateid" class="form-control select2" required>
                                    <option value="">-- Select Template --</option>
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->templateid }}">{{ $tpl->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Diagnosis Name (Dropdown with Search) --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">Diagnosis Name <span class="text-danger">*</span></label>
                                <select id="name" class="form-control select2-tags">
                                    <option value="">-- Search or Type Diagnosis --</option>
                                    @foreach($diagnosis_list as $item)
                                        <option value="{{ $item->name }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">Clinical Note</label>
                                <input type="text" id="note" class="form-control" placeholder="e.g. Chronic, Acute" />
                            </div>
                        </div>

                        {{-- Add Button --}}
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <button id="btnAdd" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- List Table --}}
                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-bordered table-sm" id="diagTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:60px;" class="text-center">#</th>
                                    <th>Diagnosis Name</th>
                                    <th>Note</th>
                                    <th style="width:100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="4" class="text-center text-muted p-3">Select a template to load diagnosis list.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .select2-container--bootstrap4 .select2-selection--single { height: calc(2.25rem + 2px) !important; }
    #diagTable thead th { border-top: 0; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function () {
    // Select2 Initialization
    $('.select2').select2({ theme: 'bootstrap4' });
    
    // Select2 with Tagging (Allow new diagnosis entry)
    $('.select2-tags').select2({ 
        theme: 'bootstrap4',
        tags: true, 
        placeholder: "-- Search or Type Diagnosis --",
        allowClear: true
    });

    const $template = $('#templateid');
    const $name = $('#name');
    const $note = $('#note');
    const $tbody = $('#diagTable tbody');
    const $msg = $('#msgBox');
    const csrf = $('meta[name="csrf-token"]').attr('content');

    function showMsg(type, text){
        const cls = type === 'success' ? 'alert-success border-0 shadow-sm' : 'alert-danger border-0 shadow-sm';
        $msg.html(`<div class="alert ${cls} py-2 mb-3">${text}</div>`);
        setTimeout(()=> $msg.fadeOut('slow', () => $msg.html('').show()), 3000);
    }

    function render(rows){
        if(!rows.length){
            $tbody.html(`<tr><td colspan="4" class="text-center text-muted p-3">No diagnosis found.</td></tr>`);
            return;
        }
        let html = rows.map((r, i) => `
            <tr data-id="${r.id}">
                <td class="text-center">${i + 1}</td>
                <td class="font-weight-bold text-dark">${r.name}</td>
                <td class="text-muted small">${r.note || ''}</td>
                <td class="text-center">
                    <button class="btn btn-link text-danger btn-sm btnDel p-0"><i class="fas fa-trash-alt"></i> Delete</button>
                </td>
            </tr>
        `).join('');
        $tbody.html(html);
    }

    function loadList(){
        const templateid = $template.val();
        if(!templateid){
            $tbody.html(`<tr><td colspan="4" class="text-center text-muted p-3">Select a template to load diagnosis list.</td></tr>`);
            return;
        }

        $.get("{{ route('templates.diagnosis.ajax.list') }}", { templateid }, function(res){
            if(res.ok) render(res.rows);
        });
    }

    $template.on('change', loadList);

    $('#btnAdd').on('click', function(e){
        e.preventDefault();
        const templateid = $template.val();
        const name = $name.val();
        const note = $note.val().trim();

        if(!templateid) return showMsg('error', 'Please select a template.');
        if(!name) return showMsg('error', 'Diagnosis name is required.');

        $.ajax({
            url: "{{ route('templates.diagnosis.ajax.add') }}",
            method: "POST",
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { templateid, name, note },
            success: function(res){
                if(res.ok){
                    showMsg('success', 'Diagnosis added successfully.');
                    $name.val(null).trigger('change');
                    $note.val('');
                    loadList();
                }
            },
            error: function(xhr){
                showMsg('error', xhr.responseJSON?.message || 'Error adding diagnosis.');
            }
        });
    });

    $tbody.on('click', '.btnDel', function(){
        if(!confirm('Delete this diagnosis?')) return;
        const id = $(this).closest('tr').data('id');

        $.ajax({
            url: "{{ url('templates/diagnosis/ajax') }}/" + id,
            method: "DELETE",
            headers: { 'X-CSRF-TOKEN': csrf },
            success: function(res){
                if(res.ok){
                    showMsg('success', 'Diagnosis deleted.');
                    loadList();
                }
            }
        });
    });

})();
</script>
@endpush