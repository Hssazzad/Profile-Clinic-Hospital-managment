@extends('adminlte::page')

@section('title', 'Template Advice')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Template Advice (???????)</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-comment-medical mr-1"></i> Add Advice</h3>
                    </div>
                    <form id="adviceForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Select Template <span class="text-danger">*</span></label>
                                <select name="templateid" id="templateid" class="form-control select2 shadow-none">
                                    <option value="">-- Choose Template --</option>
                                    @foreach($templates as $temp)
                                        <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Advice (???????) <span class="text-danger">*</span></label>
                                <textarea id="advice_text" class="form-control" rows="4" placeholder="????: ?????? ???? ??? ?????, ?????? ????? ????..."></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="btnAddAdvice" class="btn btn-warning btn-block shadow-sm">
                                <b><i class="fas fa-plus mr-1"></i> Add Advice</b>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Added Advice List</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px" class="text-center">#</th>
                                        <th>Advice Detail</th>
                                        <th style="width: 80px" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="adviceTableBody">
                                    <tr>
                                        <td colspan="3" class="text-center p-4 text-muted">Select a template to view advices</td>
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
<script>
    $(document).ready(function() {
        // ???????? ???????? ???? ????? ??? ???
        $('#templateid').on('change', function() {
            loadAdviceList();
        });

        // AJAX ?? ??????? Advice ??? ???
        $('#btnAddAdvice').on('click', function() {
            let data = {
                _token: "{{ csrf_token() }}",
                templateid: $('#templateid').val(),
                advice: $('#advice_text').val()
            };

            if(!data.templateid || !data.advice) {
                alert('Please select a template and enter advice!');
                return;
            }

            $.post("{{ route('templates.advice.ajax.add') }}", data, function(res) {
                if(res.ok) {
                    $('#advice_text').val('');
                    loadAdviceList();
                }
            }).fail(function(xhr) {
                alert('Error: ' + (xhr.responseJSON.message || 'Something went wrong'));
            });
        });
    });

    // ????? ??? ???? ?????
    function loadAdviceList() {
        let tid = $('#templateid').val();
        if(!tid) return;

        $.get("{{ route('templates.advice.ajax.list') }}", {templateid: tid}, function(res) {
            let html = '';
            if(res.rows && res.rows.length > 0) {
                res.rows.forEach((row, i) => {
                    html += `<tr>
                        <td class="text-center">${i+1}</td>
                        <td class="font-weight-bold text-dark">${row.advice}</td>
                        <td class="text-center">
                            <button onclick="deleteAdvice(${row.id})" class="btn btn-xs btn-outline-danger shadow-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="3" class="text-center">No advice found for this template</td></tr>';
            }
            $('#adviceTableBody').html(html);
        });
    }

    // ????? ???? ?????
    function deleteAdvice(id) {
        if(!confirm('Are you sure you want to delete this advice?')) return;

        $.ajax({
            url: `/templates/advice/ajax/${id}`,
            type: 'DELETE',
            data: { _token: "{{ csrf_token() }}" },
            success: function() {
                loadAdviceList();
            }
        });
    }
</script>
@stop