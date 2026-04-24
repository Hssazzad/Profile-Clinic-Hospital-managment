@extends('adminlte::page')

@section('title', 'Confirmed Invoices | Professor Clinic')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0 page-main-title">
            <span class="page-title-icon"><i class="fas fa-file-invoice"></i></span>
            Confirmed Invoices
        </h1>
        <ol class="breadcrumb mt-1 p-0" style="background:transparent;font-size:12px">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">Confirmed Invoices</li>
        </ol>
    </div>
</div>
@stop

@section('content')

<div id="save-alert" class="alert d-none mb-3" role="alert"></div>

{{-- Stats Row --}}
<div class="stats-row mb-4">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-file-invoice"></i></div>
        <div>
            <div class="stat-label">Total Bills</div>
            <div class="stat-value">{{ number_format($stats['total_bills']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="stat-label">Total Due</div>
            <div class="stat-value">? {{ number_format($stats['total_due'], 0) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-calendar-day"></i></div>
        <div>
            <div class="stat-label">Today Bills</div>
            <div class="stat-value">{{ number_format($stats['today_bills']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e0f2f1;color:#00695c"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-label">Today Revenue</div>
            <div class="stat-value">? {{ number_format($stats['today_revenue'], 0) }}</div>
        </div>
    </div>
</div>

<div class="pc-card">
    <div class="pc-card-header">
        <div class="pc-card-title">
            <span class="pc-icon-wrap"><i class="fas fa-receipt"></i></span>
            <div>
                <h5 class="mb-0">All Saved Bills</h5>
                <small class="text-muted">Investigation and confirmed invoices list</small>
            </div>
        </div>
        <span class="pc-badge">{{ $pastPayments->total() }} Records</span>
    </div>

    {{-- Search + Filter --}}
    <div class="pc-search-bar">
        <form method="GET" action="{{ route('billing.discharge.index') }}" id="paySearchForm"
              style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <div class="pc-search-group" style="flex:1; min-width:200px;">
                <span class="pc-search-icon"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="pc-search-input"
                       value="{{ request('search') }}"
                       placeholder="Search Name, receipt no, or mobile">
                <button type="submit" class="pc-search-btn">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
            </div>
            <select name="status_filter" class="filter-select" onchange="this.form.submit()">
                <option value="all"     {{ request('status_filter','all') === 'all'     ? 'selected' : '' }}>All Status</option>
                <option value="paid"    {{ request('status_filter') === 'paid'    ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ request('status_filter') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="due"     {{ request('status_filter') === 'due'     ? 'selected' : '' }}>Due</option>
            </select>
            @if(request()->has('search') || request()->has('status_filter'))
                <a href="{{ route('billing.discharge.index') }}" class="btn btn-secondary btn-sm" style="height:32px; padding-top:6px;">Clear</a>
            @endif
        </form>
    </div>

    <div class="pc-card-body pt-0">
        <div class="pc-table-wrap">
            <table class="pc-table" id="payTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>RECEIPT NO</th>
                        <th>PATIENT</th>
                        <th>DATE</th>
                        <th style="text-align:right">TOTAL</th>
                        <th style="text-align:right">PAID</th>
                        <th style="text-align:right">DUE</th>
                        <th style="text-align:center">STATUS</th>
                        <th style="text-align:center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pastPayments as $pp)
                    <tr>
                        <td class="text-muted">{{ $loop->iteration + $pastPayments->firstItem() - 1 }}</td>
                        <td>
                            <span class="pc-adm-badge">{{ $pp->BillNo }}</span>
                        </td>
                        <td>
                            <div class="pc-name-cell">
                                <div>
                                    <strong style="font-size:13px;">{{ $pp->PatientName ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $pp->PatientCode ?? '' }} | {{ $pp->MobileNo ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px; color:#666;">
                            {{ $pp->PaymentDate ? \Carbon\Carbon::parse($pp->PaymentDate)->format('Y-m-d') : '' }}
                        </td>
                        <td style="text-align:right; font-weight:600; font-size:12px;">
                            ? {{ number_format($pp->TotalBill, 0) }}
                        </td>
                        <td style="text-align:right; font-weight:600; font-size:12px; color:#2e7d32;">
                            ? {{ number_format($pp->PaidAmount, 0) }}
                        </td>
                        <td style="text-align:right; font-weight:600; font-size:12px;
                                   color:{{ $pp->DueAmount > 0 ? '#c62828' : '#aaa' }}">
                            ? {{ number_format($pp->DueAmount, 0) }}
                        </td>
                        <td style="text-align:center;">
                            @if(strtolower($pp->Status) === 'paid')
                                <span class="pc-status-paid">Paid</span>
                            @elseif(strtolower($pp->Status) === 'partial')
                                <span class="pc-status-partial">Partial</span>
                            @else
                                <span class="pc-status-due">Due</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; gap:4px; justify-content:center;">
                                <a href="{{ url('Billing/payment/'.$pp->ID.'/print') }}" target="_blank" class="pc-print-btn" style="padding:4px 8px; border-radius:5px; text-decoration:none;">
                                    <i class="fas fa-print"></i>
                                </a>
                                <button class="pc-view-btn" onclick="viewReceipt({{ $pp->ID }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="pc-delete-btn"
                                        onclick="deleteRecord({{ $pp->ID }}, this)"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="pc-empty">
                                <i class="fas fa-receipt"></i>
                                <p>No confirmed bills found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pastPayments->hasPages())
        <div class="pc-pagination mt-3">
            <small class="text-muted">
                Showing {{ $pastPayments->firstItem() }} to {{ $pastPayments->lastItem() }}
                of {{ $pastPayments->total() }} entries
            </small>
            {{ $pastPayments->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>


{{-- MODAL: View past receipt --}}
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#00796B,#00695C);color:#fff;border:none;padding:18px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);
                                display:flex;align-items:center;justify-content:center;font-size:18px;margin-right:12px">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold">Investigation Bill</h5>
                        <small style="opacity:.9" id="modal-subtitle">Loading</small>
                    </div>
                </div>
                <div style="display:flex;gap:8px">
                    <button class="pc-modal-print-btn" onclick="printModal()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;opacity:.8"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="modal-loading" style="text-align:center;padding:60px;color:#888">
                    <i class="fas fa-spinner fa-spin" style="font-size:30px;color:#00796B"></i>
                    <p class="mt-3">Loading receipt</p>
                </div>
                <div id="modal-error" class="d-none" style="text-align:center;padding:60px;color:#e53935">
                    <i class="fas fa-exclamation-triangle" style="font-size:32px"></i>
                    <p class="mt-3" id="modal-error-msg">Failed to load.</p>
                </div>
                <div id="modal-rx-area" class="d-none">
                    <div id="modal-prescription-print-area" style="padding:20px 24px"></div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafbfd;border-top:1px solid #eee;padding:14px 24px">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<iframe id="print-iframe" style="position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none" title="Print Frame"></iframe>

@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --pc:#00796B; --pc-deep:#00695C; --pc-light:#E0F2F1; --pc-soft:#B2DFDB;
    --text:#1a2332; --muted:#6b7a90; --border:#e4e9f0; --bg:#f0f4f0;
    --red:#c62828; --amber:#f9a825; --green:#2e7d32;
    --radius:10px; --shadow:0 4px 16px rgba(0,0,0,.08);
    --font:'DM Sans','Hind Siliguri',Arial,sans-serif;
}
*,*::before,*::after{box-sizing:border-box}
body,.content-wrapper{background:var(--bg)!important;font-family:var(--font)}
.page-main-title{font-size:22px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:10px}
.page-title-icon{width:38px;height:38px;border-radius:10px;background:var(--pc-light);display:inline-flex;align-items:center;justify-content:center;color:var(--pc);font-size:17px}

/* Stats */
.stats-row{display:flex;gap:14px;flex-wrap:wrap}
.stat-card{background:#fff;border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:14px;flex:1;min-width:160px;box-shadow:0 2px 8px rgba(0,0,0,.06);border:1px solid var(--border)}
.stat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.stat-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px}
.stat-value{font-size:16px;font-weight:700;color:var(--text)}

/* Card */
.pc-card{background:#fff;border-radius:14px;box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden;margin-bottom:24px}
.pc-card-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#fafbfd;flex-wrap:wrap;gap:10px}
.pc-card-title{display:flex;align-items:center;gap:12px}
.pc-card-title h5,.pc-card-title h6{font-size:14px; font-weight:600;}
.pc-icon-wrap{width:36px;height:36px;border-radius:8px;background:var(--pc-light);display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--pc);flex-shrink:0}
.pc-card-body{padding:20px}
.pc-card-footer{padding:12px 20px;border-top:1px solid var(--border);background:#fafbfd;display:flex;align-items:center;justify-content:space-between}
.pc-badge{background:var(--pc-light);color:var(--pc-deep);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600}

/* Search */
.pc-search-bar{padding:10px 20px;background:#fafbff;border-bottom:2px solid var(--pc-soft)}
.pc-search-group{display:flex;align-items:center;background:#fff;border:2px solid var(--border);border-radius:8px;overflow:hidden;transition:border-color .2s}
.pc-search-group:focus-within{border-color:var(--pc);box-shadow:0 0 0 3px rgba(0,121,107,.1)}
.pc-search-icon{padding:0 10px;color:#aab;font-size:14px}
.pc-search-input{flex:1;border:none;outline:none;padding:8px 6px;font-size:13px;background:transparent;color:var(--text);font-family:var(--font)}
.pc-search-btn{border:none;padding:9px 18px;font-size:12px;font-weight:600;cursor:pointer;background:var(--pc);color:#fff;transition:background .2s;white-space:nowrap}
.pc-search-btn:hover{background:var(--pc-deep)}
.filter-select{border:2px solid var(--border);border-radius:8px;padding:7px 10px;font-size:12px;font-family:var(--font);outline:none;cursor:pointer;background:#fff;color:var(--text);transition:border-color .2s}
.filter-select:focus{border-color:var(--pc)}

/* Table */
.pc-table-wrap{overflow-x:auto;overflow-y:auto}
.pc-table{width:100%;border-collapse:separate;border-spacing:0}
.pc-table thead th{background:#f0faf8;color:var(--text);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;padding:12px;border-bottom:2px solid var(--pc-soft);white-space:nowrap;position:sticky;top:0;z-index:10}
.pc-table tbody tr{transition:background .15s}
.pc-table tbody tr:hover{background:#f0faf8}
.pc-table tbody td{padding:12px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);vertical-align:middle}
.pc-table tbody tr:last-child td{border-bottom:none}
.pc-adm-badge{background:#e0f2f1;color:var(--pc-deep);border-radius:4px;padding:3px 8px;font-size:12px;font-weight:700;font-family:monospace}
.pc-status-paid{background:#e8f5e9;color:#2e7d32;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:600}
.pc-status-partial{background:#fff3e0;color:#f57c00;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:600}
.pc-status-due{background:#ffebee;color:var(--red);border-radius:4px;padding:3px 8px;font-size:11px;font-weight:600}
.pc-name-cell{display:flex;align-items:center;gap:8px}
.pc-empty{text-align:center;padding:40px 20px;color:var(--muted)}
.pc-empty i{font-size:40px;margin-bottom:12px;opacity:.5;display:block}
.pc-empty p{margin:0;font-size:14px}

.pc-pagination{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.pc-pagination .pagination{margin:0}

/* Actions */
.pc-print-btn{background:#00bfa5;color:#fff;border:1px solid #00bfa5;transition:all .2s; cursor:pointer;}
.pc-print-btn:hover{background:#00897b;color:#fff;}
.pc-view-btn{background:#1976D2;color:#fff;border:none;border-radius:5px;padding:4px 8px;font-size:12px;cursor:pointer;transition:all .2s}
.pc-view-btn:hover{background:#1565C0}
.pc-delete-btn{background:#ffebee;color:var(--red);border:1px solid #ffcdd2;border-radius:5px;padding:4px 8px;font-size:12px;cursor:pointer;transition:all .2s}
.pc-delete-btn:hover{background:var(--red);color:#fff}
.pc-modal-print-btn{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.pc-modal-print-btn:hover{background:rgba(255,255,255,.3)}

/* Print template for modal */
.print-bill-wrapper{background:#fff;max-width:560px;margin:0 auto;border:1px solid #ccc;font-family:'Hind Siliguri',Arial,sans-serif}
.print-letterhead{display:flex;justify-content:space-between;align-items:flex-start;padding:16px;border-bottom:2px solid #00796B}
.print-lh-left{font-size:11px;color:#444;min-width:100px}
.print-reg-label{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.4px}
.print-reg-val{font-size:12px;font-weight:700;color:#00695C}
.print-lh-center{flex:1;text-align:center;padding:0 10px}
.print-clinic-name{font-size:18px;font-weight:700;color:#00695C;letter-spacing:.4px}
.print-clinic-addr{font-size:11px;color:#555}
.print-clinic-cell{font-size:11px;color:#777}
.print-lh-right{font-size:11px;color:#555;text-align:right;line-height:1.7;min-width:100px}
.print-patient-row{display:flex;flex-wrap:wrap;gap:8px;padding:10px 16px;background:#f8f9fa;border-bottom:1px solid #ddd}
.print-pf{flex:1;min-width:100px}
.print-pf-label{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.3px;display:block}
.print-pf-val{font-size:12px;font-weight:600;color:#333}
.print-bill-title{text-align:center;font-size:12px;font-weight:700;letter-spacing:2px;padding:7px;background:#f0faf8;border-bottom:2px solid #B2DFDB;color:#00695C;text-transform:uppercase}
.print-bill-table{width:100%;border-collapse:collapse;font-size:12px}
.print-bill-table thead th{background:#f0faf8;color:#1a2332;font-size:10px;font-weight:700;text-transform:uppercase;padding:6px 12px;border-bottom:2px solid #B2DFDB;text-align:left}
.print-bill-table tbody td{padding:6px 12px;border-bottom:1px solid #eee;vertical-align:middle}
.print-bill-table tfoot td{padding:7px 12px;font-weight:700}
.print-footer-note{font-size:12px;color:#444;padding:9px 16px;border-top:1px dashed #ccc;background:#fafafa;font-family:'Hind Siliguri',Arial,sans-serif}
.print-sig-row{display:flex;justify-content:space-between;align-items:flex-end;padding:12px 16px}
.print-sig-left{font-size:12px;color:#555}
.print-sig-right{text-align:center}
.print-sig-line{border-bottom:1px solid #999;width:110px;margin-bottom:4px}
.print-sig-label{font-size:11px;color:#777}

.d-none{display:none!important}

@media print{
    *{visibility:hidden!important}
    #prescription-print-area,#prescription-print-area *{visibility:visible!important}
    #prescription-print-area{position:fixed!important;top:0!important;left:0!important;width:100%!important;margin:0!important;padding:0!important;z-index:99999!important;}
    .print-bill-wrapper{box-shadow:none!important;border:1px solid #000!important;max-width:100%!important;width:100%!important;}
    @page{margin:8mm;size:A5}
}
</style>
@stop

@section('js')
<script>
function getCsrfToken(){
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
}
function esc(s){
    if(!s) return '';
    return s.toString().replace(/[&<>"']/g, function(m){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
    });
}
function fmtDate(d){
    if(!d) return '';
    return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});
}
function showAlert(msg, type){
    var el = document.getElementById('save-alert');
    el.className = 'alert alert-'+(type||'success')+' mb-3';
    el.textContent = msg;
    el.classList.remove('d-none');
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(function(){ el.classList.add('d-none'); }, 5000);
}


/* -- viewReceipt modal ------------------------------------- */
function viewReceipt(id){
    $('#receiptModal').modal('show');
    document.getElementById('modal-loading').style.display = '';
    document.getElementById('modal-error').classList.add('d-none');
    document.getElementById('modal-rx-area').classList.add('d-none');

    fetch('{{ url("Billing/DischargeBillPayment/detail") }}/'+id, {headers:{'Accept':'application/json'}})
    .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
    .then(function(resp){ if(resp.success) renderModal(resp.data); else showModalError(resp.message||'Not found'); })
    .catch(function(e){ showModalError(e.message); });
}

function renderModal(d){
    var tests   = d.tests || d.items || [];
    var gross   = parseFloat(d.gross_amount    || d.total_amount || 0);
    var less    = parseFloat(d.discount_amount || d.discount     || 0);
    var net     = parseFloat(d.total_amount    || 0);
    var adv     = parseFloat(d.paid_amount     || 0);
    var due     = parseFloat(d.due_amount      || 0);
    var collBy  = d.received_by || d.collected_by || '';

    var rows = tests.map(function(it, i){
        var name   = it.test_name || it.service_name || it.name || '';
        var amount = it.subtotal  || it.amount       || it.price || 0;
        return '<tr><td style="text-align:center">'+(i+1)+'</td><td>'+esc(name)+'</td><td style="text-align:right">'+Number(amount).toLocaleString()+'</td></tr>';
    }).join('');

    var footerRows =
        '<tr style="background:#f0faf8"><td colspan="2" style="text-align:right;font-weight:700">Total</td><td style="text-align:right;font-weight:700">'+Number(gross).toLocaleString()+'</td></tr>'+
        (less>0?'<tr style="color:#c62828"><td colspan="2" style="text-align:right">Less (Discount)</td><td style="text-align:right"> '+Number(less).toLocaleString()+'</td></tr>':'')+
        '<tr style="background:#e0f2f1"><td colspan="2" style="text-align:right;font-weight:700;color:#00695c">Net Total</td><td style="text-align:right;font-weight:700;color:#00695c">'+Number(net).toLocaleString()+'</td></tr>'+
        '<tr style="background:#fff8e1"><td colspan="2" style="text-align:right">Paid</td><td style="text-align:right;font-weight:700">'+Number(adv).toLocaleString()+'</td></tr>'+
        (due>0?'<tr style="background:#ffebee"><td colspan="2" style="text-align:right;font-weight:700;color:#c62828">Due</td><td style="text-align:right;font-weight:700;color:#c62828">'+Number(due).toLocaleString()+'</td></tr>':'');

    document.getElementById('modal-prescription-print-area').innerHTML =
        '<div class="print-bill-wrapper" id="prescription-print-area">'+
        '<div class="print-letterhead">'+
            '<div class="print-lh-left"><div class="print-reg-label">Registration no</div><div class="print-reg-val">'+esc(d.patient_code||'')+'</div></div>'+
            '<div class="print-lh-center"><div class="print-clinic-name">Professor Clinic</div><div class="print-clinic-addr">Majhira, Shajahanpur, Bogura</div><div class="print-clinic-cell">Cell: 01720-039006</div></div>'+
            '<div class="print-lh-right"><div>01713-740680</div><div>01720-039005</div><div>01720-039006</div><div>01720-039007</div><div>01720-039008</div></div>'+
        '</div>'+
        '<div class="print-patient-row">'+
            '<div class="print-pf"><span class="print-pf-label">Patient\'s name</span><span class="print-pf-val">'+esc(d.patient_name||'')+'</span></div>'+
            '<div class="print-pf"><span class="print-pf-label">Age</span><span class="print-pf-val">'+esc(d.patient_age||'')+'</span></div>'+
            '<div class="print-pf"><span class="print-pf-label">Date</span><span class="print-pf-val">'+fmtDate(d.payment_date)+'</span></div>'+
            '<div class="print-pf"><span class="print-pf-label">Receipt</span><span class="print-pf-val font-weight-bold">'+esc(d.receipt_no||'')+'</span></div>'+
        '</div>'+
        '<div class="print-bill-title">INVESTIGATION BILL</div>'+
        '<table class="print-bill-table"><thead><tr><th style="width:42px;text-align:center">Sl.</th><th>Investigation</th><th style="text-align:right">Amount (?)</th></tr></thead>'+
        '<tbody>'+rows+'</tbody><tfoot>'+footerRows+'</tfoot></table>'+
        '<div class="print-footer-note">????? ?? ?????? ?????  ?????? ????? ????? ????? ???</div>'+
        '<div class="print-sig-row"><div class="print-sig-left">Collected by: <strong>'+esc(collBy)+'</strong></div>'+
        '<div class="print-sig-right"><div class="print-sig-line"></div><div class="print-sig-label">Authorized Signature</div></div></div>'+
        '</div>';

    document.getElementById('modal-subtitle').textContent = (d.patient_name||'')+'  '+(d.receipt_no||'');
    document.getElementById('modal-loading').style.display = 'none';
    document.getElementById('modal-rx-area').classList.remove('d-none');
}
function showModalError(msg){
    document.getElementById('modal-error-msg').textContent = msg;
    document.getElementById('modal-loading').style.display = 'none';
    document.getElementById('modal-error').classList.remove('d-none');
}

/* -- deleteRecord ------------------------------------------ */
function deleteRecord(id, btn){
    if(!confirm('Delete this bill? This cannot be undone.')) return;
    btn.disabled = true;
    fetch('{{ url("Billing/DischargeBillPayment/delete") }}/'+id, {
        method:'DELETE',
        headers:{'Accept':'application/json','X-CSRF-TOKEN':getCsrfToken()}
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if(d.success){
            var row = btn.closest('tr');
            if(row) row.remove();
            showAlert('Bill deleted.','success');
        } else {
            showAlert(d.message||'Delete failed.','danger');
            btn.disabled = false;
        }
    })
    .catch(function(e){ showAlert('Error: '+e.message,'danger'); btn.disabled = false; });
}

/* -- print ------------------------------------------------- */
function printModal(){
    var area = document.getElementById('modal-prescription-print-area');
    if(!area||!area.innerHTML.trim()){ alert('No receipt to print.'); return; }
    var html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'+
        '<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">'+
        '<style>body{margin:0;padding:8mm;font-family:"Hind Siliguri",Arial,sans-serif;-webkit-print-color-adjust:exact;print-color-adjust:exact}'+
        '.print-bill-wrapper{max-width:100%;margin:0 auto;border:1px solid #ccc}'+
        '.print-letterhead{display:flex;justify-content:space-between;align-items:flex-start;padding:16px;border-bottom:2px solid #00796B}'+
        '.print-lh-center{flex:1;text-align:center;padding:0 10px}.print-clinic-name{font-size:18px;font-weight:700;color:#00695C}'+
        '.print-clinic-addr,.print-clinic-cell{font-size:11px;color:#555}.print-lh-left{font-size:11px;color:#444;min-width:100px}'+
        '.print-lh-right{font-size:11px;color:#555;text-align:right;line-height:1.7;min-width:100px}'+
        '.print-reg-label{font-size:10px;color:#888;text-transform:uppercase}.print-reg-val{font-size:12px;font-weight:700;color:#00695C}'+
        '.print-patient-row{display:flex;flex-wrap:wrap;gap:8px;padding:10px 16px;background:#f8f9fa;border-bottom:1px solid #ddd}'+
        '.print-pf{flex:1;min-width:100px}.print-pf-label{font-size:10px;color:#888;text-transform:uppercase;display:block}'+
        '.print-pf-val{font-size:12px;font-weight:600;color:#333}'+
        '.print-bill-title{text-align:center;font-size:12px;font-weight:700;letter-spacing:2px;padding:7px;background:#f0faf8;border-bottom:2px solid #B2DFDB;color:#00695C;text-transform:uppercase}'+
        '.print-bill-table{width:100%;border-collapse:collapse;font-size:12px}'+
        '.print-bill-table thead th{background:#f0faf8;font-size:10px;font-weight:700;text-transform:uppercase;padding:6px 12px;border-bottom:2px solid #B2DFDB;text-align:left}'+
        '.print-bill-table tbody td,.print-bill-table tfoot td{padding:6px 12px;border-bottom:1px solid #eee;vertical-align:middle}'+
        '.print-footer-note{font-size:12px;color:#444;padding:9px 16px;border-top:1px dashed #ccc;background:#fafafa}'+
        '.print-sig-row{display:flex;justify-content:space-between;align-items:flex-end;padding:12px 16px}'+
        '.print-sig-line{border-bottom:1px solid #999;width:110px;margin-bottom:4px}.print-sig-label{font-size:11px;color:#777}'+
        '.font-weight-bold{font-weight:700}@page{margin:8mm;size:A5}</style>'+
        '</head><body>'+area.innerHTML+
        '<script>window.onload=function(){window.print();}<\/script></body></html>';
    var iframe = document.getElementById('print-iframe');
    iframe.onload = function(){ setTimeout(function(){ try{ iframe.contentWindow.print(); }catch(e){} },500); iframe.onload=null; };
    iframe.srcdoc = html;
}

</script>
@stop