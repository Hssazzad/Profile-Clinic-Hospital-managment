@extends('adminlte::page')

@section('title', 'Template Investigation')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Template Investigation</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Investigation</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-plus mr-1"></i> Add Investigation</h3>
                    </div>
                    <form id="investigationForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Select Template</label>
                                <select name="templateid" id="templateid" class="form-control select2 shadow-none">
                                    <option value="">-- Select Template --</option>
                                    @foreach($templates as $temp)
                                        <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Investigation Name <span class="text-danger">*</span></label>
                                <input type="text" id="investigation_name" class="form-control" placeholder="Enter investigation name...">
                            </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea id="note" class="form-control" rows="2" placeholder="Instructions..."></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="btnAdd" class="btn btn-primary btn-block shadow-sm">
                                <i class="fas fa-save mr-1"></i> Add Investigation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Investigation List</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover m-0">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 50px">#</th>
                                        <th>Investigation Name</th>
                                        <th>Note</th>
                                        <th style="width: 80px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="investigationTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center p-4 text-muted">
                                            Please select a template to add investigations.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
{{-- Select2 CSS এবং JS নিশ্চিত করুন আপনার AdminLTE-এ আছে অথবা এখান থেকে লোড করুন --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Select2 Initialize
        $('.select2').select2({
            placeholder: "-- Select Template --",
            allowClear: true,
            width: '100%'
        });

        $('#templateid').on('change', function() {
            loadList();
        });

        $('#btnAdd').on('click', function() {
            let data = {
                _token: "{{ csrf_token() }}",
                templateid: $('#templateid').val(),
                name: $('#investigation_name').val(),
                note: $('#note').val()
            };

            if(!data.templateid || !data.name) {
                alert('Please select Template and enter Investigation Name!');
                return;
            }

            $.post("{{ route('templates.investigation.ajax.add') }}", data, function(res) {
                if(res.ok) {
                    // Reset form fields
                    $('#investigation_name').val('');
                    $('#note').val('');
                    loadList();
                }
            }).fail(function(xhr) { 
                alert('Error: ' + (xhr.responseJSON.message || 'Something went wrong')); 
            });
        });
    });

    function loadList() {
        let tid = $('#templateid').val();
        if(!tid) {
            $('#investigationTableBody').html('<tr><td colspan="4" class="text-center p-4 text-muted">Please select a template to add investigations.</td></tr>');
            return;
        }

        // Show loading state
        $('#investigationTableBody').html('<tr><td colspan="4" class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading investigations...</td></tr>');

        $.get("{{ route('templates.investigation.ajax.list') }}", {templateid: tid}, function(res) {
            let html = '';
            if(res.rows && res.rows.length > 0) {
                res.rows.forEach((row, i) => {
                    html += `<tr class="text-center">
                        <td>${i+1}</td>
                        <td class="text-left font-weight-bold text-primary">${row.name}</td>
                        <td class="text-left">${row.note || ''}</td>
                        <td>
                            <button onclick="deleteRow(${row.id})" class="btn btn-xs btn-outline-danger shadow-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center p-4">No investigations found for this template</td></tr>';
            }
            $('#investigationTableBody').html(html);
        }).fail(function() {
            $('#investigationTableBody').html('<tr><td colspan="4" class="text-center p-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load investigations</td></tr>');
        });
    }

    function deleteRow(id) {
        if(confirm('Are you sure you want to delete this investigation from template?')) {
            $.ajax({
                url: `/templates/investigation/ajax/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function() {
                    loadList(); // Reload list after deletion
                },
                error: function() {
                    alert('Failed to delete investigation. Please try again.');
                }
            });
        }
    }
</script>
@stop