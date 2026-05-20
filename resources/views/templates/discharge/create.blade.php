@extends('adminlte::page')

@section('title', 'Template Discharge')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Template Discharge Note</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-alt mr-1"></i> Add Discharge Summary</h3>
                    </div>
                    <form id="dischargeForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Select Template</label>
                                <select name="templateid" id="templateid" class="form-control select2">
                                    <option value="">-- Choose Template --</option>
                                    @foreach($templates as $temp)
                                        <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Treatment Given (?? ??????? ????? ?????)</label>
                                <textarea id="treatment_given" class="form-control" rows="3" placeholder="Describe treatments..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Condition at Discharge (????? ??? ??????)</label>
                                <textarea id="condition" class="form-control" rows="2" placeholder="e.g. Clinically Stable/Improved"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Follow-up Advice (??????? ???????)</label>
                                <textarea id="follow_up" class="form-control" rows="2" placeholder="e.g. Visit OPD after 7 days"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="btnSaveDischarge" class="btn btn-danger btn-block shadow-sm">
                                <i class="fas fa-save mr-1"></i> Save Discharge Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye mr-1"></i> Saved Discharge Summary</h3>
                    </div>
                    <div class="card-body" id="dischargePreview">
                        <div class="text-center p-5">
                            <i class="fas fa-notes-medical fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Select a template to view discharge summary details</p>
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
        // ???????? ???????? ??? ???? ??? ???
        $('#templateid').on('change', function() {
            loadDischargeData();
        });

        // AJAX ??? ??????? (????? ?????????? ??????? URL ??? ??? ?????)
        $('#btnSaveDischarge').on('click', function() {
            let data = {
                _token: "{{ csrf_token() }}",
                templateid: $('#templateid').val(),
                treatment: $('#treatment_given').val(),
                condition: $('#condition').val(),
                follow_up: $('#follow_up').val()
            };

            if(!data.templateid) {
                alert('Please select a template!');
                return;
            }

            $.post("{{ route('templates.discharge.ajax.add') }}", data, function(res) {
                if(res.ok) {
                    alert('Discharge summary updated!');
                    loadDischargeData();
                }
            });
        });
    });

    function loadDischargeData() {
        let tid = $('#templateid').val();
        if(!tid) return;

        // ????? ????? ???? ??? ???? ???? ??????
        $('#dischargePreview').html('<p class="text-center">Loading data...</p>');
    }
</script>
@stop