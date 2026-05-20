{{-- resources/views/templates/prescription_menu_list.blade.php --}}
@extends('adminlte::page')

@section('title', 'Template Menu List')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h5><i class="fas fa-prescription"></i> Template Menu List</h5>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                <li class="breadcrumb-item active">Menu List</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
@import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla&display=swap');
body { background: #d9d9d9 !important; }

.template-selector {
    max-width: 960px; margin: 18px auto 16px;
    background: #fff; border: 1px solid #bbb; border-radius: 4px;
    padding: 14px 20px; box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.btn-load {
    background: #1a6b2e; color: #fff; border: none;
    padding: 8px 0; border-radius: 3px; font-size: 14px;
    cursor: pointer; width: 100%;
}
.btn-load:hover { background: #145424; }

.rx-sheet {
    max-width: 960px; margin: 0 auto 30px;
    background: #fff; border: 2px solid #2c3e50;
    box-shadow: 0 4px 18px rgba(0,0,0,.22);
    display: none; page-break-after: always;
}
.rx-sheet.visible { display: block; }

.sheet-tag {
    background: #2c3e50; color: #fff; font-size: 11px; font-weight: 700;
    padding: 3px 12px; letter-spacing: 1px; text-transform: uppercase; text-align: right;
}

.clinic-header-blue {
    background: #fff; padding: 12px 24px 8px;
    border-bottom: 2px solid #1a3c5e; text-align: center;
}
.logo-row { display: flex; align-items: center; justify-content: center; gap: 10px; }
.clinic-logo-c {
    width:46px;height:46px;border-radius:50%;background:#1a3c5e;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:900;font-size:13px;flex-shrink:0;
}
.clinic-bn-title { font-family:'Tiro Bangla',serif;font-size:28px;font-weight:900;color:#1a3c5e;line-height:1; }
.clinic-bn-sub   { font-family:'Tiro Bangla',serif;font-size:13px;color:#333;margin-top:2px; }
.clinic-phones   { font-size:12px;color:#555;margin-top:3px; }

.rx-patient-row {
    display:flex;gap:20px;padding:7px 24px;
    background:#f8f8f8;border-bottom:1px solid #bbb;font-size:13px;
}
.rx-patient-row strong { color:#1a3c5e; }

.rx-admission-body {
    display: grid;
    grid-template-columns: 42% 58%;
    border-top: 1px solid #ddd;
}
.rx-col { padding: 14px 18px; border-right: 1px solid #bbb; }
.rx-col:last-child { border-right: none; }

/* ── sec-label: O/E · Adv — underline only as wide as the text ── */
.sec-label {
    font-weight: 800;
    font-size: 12px;
    color: #1a3c5e;
    border-bottom: 2px solid #1a3c5e;
    padding-bottom: 2px;
    margin: 12px 0 7px;
    text-transform: uppercase;
    letter-spacing: .4px;
    display: inline-block;   /* underline follows text width */
}

/* ── Rx On Admission On — underline only as wide as the text ── */
.rx-on-adm-title {
    font-size: 15px;
    font-weight: 900;
    color: #1a3c5e;
    border-bottom: 2px solid #1a3c5e;
    padding-bottom: 5px;
    margin-bottom: 12px;
    letter-spacing: .3px;
    background: none;
    display: inline-block;
    width: auto;
}

/* ── Pre-Operative Order — underline only as wide as the text ── */
.preop-plain-title {
    font-size: 13px;
    font-weight: 900;
    color: #1a3c5e;
    border-bottom: 1.5px solid #1a3c5e;
    padding-bottom: 3px;
    margin: 16px 0 8px;
    background: none;
    display: inline-block;
    width: auto;
}

/* ── Post-Operative Order On — underline only as wide as the text ── */
.post-order-plain {
    font-size: 15px;
    font-weight: 900;
    color: #1a3c5e;
    border-bottom: 2px solid #1a3c5e;
    padding-bottom: 5px;
    margin-bottom: 14px;
    background: none;
    display: inline-block;
    width: auto;
}

.blist { list-style:none;padding:0;margin:0; }
.blist li {
    font-size:13px;padding:4px 0;border-bottom:1px dotted #ddd;
    display:flex;align-items:flex-start;gap:5px;flex-wrap:wrap;
}
.blist li::before { content:"•";color:#1a3c5e;font-size:16px;flex-shrink:0; }
.blist li.red::before { color:#c0392b;font-weight:900;font-size:16px; }
.med-sub { font-size:11px;color:#555;padding-left:18px;display:block;margin-top:1px;width:100%; }

.post-op-body { padding: 20px 32px; }

.op-note-title-banner {
    background:#1a6b2e; color:#fff;
    font-family:'Tiro Bangla',serif; font-size:24px; font-weight:900;
    text-align:center; padding:9px 0; border-radius:28px;
    margin:18px 28px 20px; letter-spacing:1px;
}
.op-note-body { padding:0 20px 20px; }
.op-row {
    display:grid; grid-template-columns:170px 1fr;
    border:1.5px solid #b5d9b5; border-radius:6px;
    overflow:hidden; margin-bottom:12px;
}
.op-row-label {
    background:#f0faf0; border-right:1.5px solid #b5d9b5;
    padding:12px 10px; font-family:'Tiro Bangla',serif;
    font-size:13px; font-weight:700; color:#1a6b2e;
    display:flex; align-items:center; justify-content:center;
    text-align:center; line-height:1.5;
}
.op-row-value { padding:12px 14px; min-height:54px; font-size:13px; color:#333; background:#fff; }
.op-row.tall .op-row-label,
.op-row.tall .op-row-value { min-height:90px; }

.pdp-title-banner {
    background:#1a6b2e; color:#fff;
    font-family:'Tiro Bangla',serif; font-size:22px; font-weight:900;
    text-align:center; padding:9px 0; border-radius:28px;
    margin:18px 28px 0; letter-spacing:1px;
}
.pdp-big-area {
    min-height:260px; margin:12px 20px 0;
    border:1.5px solid #b5d9b5; border-radius:6px;
    padding:12px 14px; background:#fff;
}
.pdp-big-area ul.blist li { font-size:13px; }
.pdp-sub-banner {
    background:#1a6b2e; color:#fff;
    font-family:'Tiro Bangla',serif; font-size:16px; font-weight:900;
    display:inline-block; padding:5px 22px; border-radius:20px;
    margin:14px 20px 0; letter-spacing:.5px;
}
.pdp-inv-area {
    min-height:130px; margin:8px 20px 0;
    border-top:1.5px solid #b5d9b5;
    padding:10px 14px; background:#fff;
}
.pdp-inv-area ul.blist li { font-size:13px; }
.pdp-footer-row {
    display:flex; justify-content:space-between;
    padding:10px 24px 16px; font-size:12px;
    font-family:'Tiro Bangla',serif; color:#333;
    border-top:1px solid #ddd; margin-top:10px;
}

.cs-title-banner {
    background:#1a6b2e; color:#fff;
    font-family:'Tiro Bangla',serif; font-size:22px; font-weight:900;
    text-align:center; padding:9px 0; border-radius:28px;
    margin:18px 28px 0; letter-spacing:1px;
}
.cs-advice-area {
    min-height:180px; margin:12px 20px 0;
    border:1.5px solid #b5d9b5; border-radius:6px;
    padding:12px 14px; background:#fff;
}
.cs-advice-area ul.blist li { font-size:13px; }
.cs-special-banner {
    background:#1a6b2e; color:#fff;
    font-family:'Tiro Bangla',serif; font-size:20px; font-weight:900;
    padding:8px 24px; margin:14px 0 0; letter-spacing:.5px;
}
.cs-attraction-list { list-style:none; padding:14px 28px; margin:0; }
.cs-attraction-list li {
    font-family:'Tiro Bangla',serif; font-size:14px;
    color:#222; padding:8px 0; border-bottom:1px dotted #ccc;
    display:flex; gap:8px; align-items:flex-start;
}
.cs-attraction-list li::before { content:"►"; color:#1a6b2e; font-size:12px; flex-shrink:0; margin-top:3px; }

.clinic-header-pad { background:#fff;border-bottom:2px solid #ccc;display:flex;align-items:stretch; }
.pad-clinic-side { flex:1;padding:12px 20px;border-right:2px solid #ccc; }
.pad-doctor-side { width:260px;padding:10px 14px;text-align:right; }
.pad-clinic-name { font-family:'Tiro Bangla',serif;font-size:20px;font-weight:900;color:#1a5c35; }
.pad-clinic-addr { font-size:12px;color:#444;margin-top:2px; }
.pad-clinic-phones { font-size:11px;color:#666;margin-top:2px; }
.pad-doctor-name { font-size:15px;font-weight:800;color:#c0392b;font-family:'Tiro Bangla',serif; }
.pad-doctor-deg { font-size:11px;color:#333;margin-top:2px;line-height:1.5; }
.pad-doctor-inst { font-size:11px;color:#1a5c35;margin-top:3px;font-weight:600; }

.pad-name-row { display:flex;gap:16px;padding:7px 20px;background:#f5f5f5;border-bottom:1px solid #ddd;font-size:13px; }
.pad-name-row .field { flex:1;border-bottom:1px solid #aaa;padding-bottom:2px; }
.pad-name-row .field label { font-size:11px;color:#888;display:block; }
.pad-name-row .field .val { font-size:13px;color:#222;min-height:18px; }
.pad-cc-row { background:#e8f0fb;padding:5px 20px;font-size:12px;font-weight:700;color:#1a3c5e;border-bottom:1px solid #ccc; }

.pad-body { display:grid;grid-template-columns:220px 1fr;min-height:480px; }
.pad-left { border-right:2px solid #ccc;padding:14px;background:#fdfdfd; }
.pad-right { padding:14px 18px; }
.pad-sec-label { font-size:12px;font-weight:800;color:#1a3c5e;border-bottom:1.5px solid #1a3c5e;padding-bottom:2px;margin:10px 0 6px;text-transform:uppercase;display:inline-block; }
.pad-left-list { list-style:none;padding:0;margin:0 0 6px; }
.pad-left-list li { font-size:12.5px;padding:3px 0;border-bottom:1px dotted #ddd;display:flex;gap:5px; }
.pad-left-list li::before { content:".";color:#1a3c5e;font-size:18px;line-height:.9; }
.rx-symbol { font-size:36px;color:#1a3c5e;font-weight:900;font-style:italic;line-height:1;margin-bottom:8px; }

.pad-med-table { width:100%; border-collapse:collapse; font-size:12px; margin-top:6px; }
.pad-med-table th, .pad-med-table td { border:1px solid #999; text-align:center; padding:4px 2px; }
.pad-med-table th { background:#1a3c5e; color:#fff; font-weight:700; font-size:11px; }
.pad-med-table th.sub { background:#2a4c6e; font-size:10px; padding:3px 2px; }
.pad-med-table .med-name-col { width:35%; text-align:left; padding-left:6px; }
.pad-med-table td.med-name { text-align:left; padding-left:6px; font-size:12px; }
.pad-med-table td.med-name .med-dose { font-size:10px; color:#666; }
.pad-med-table td.chk { font-size:13px; font-weight:700; color:#1a3c5e; }
.pad-med-table td.chk-mark { font-size:14px; font-weight:900; color:#1a6b2e; }
.pad-med-table tbody tr:nth-child(even) { background:#f5f8fa; }

.pad-footer {
    border-top:2px solid #e91e8c;
    background:linear-gradient(90deg,#f8e8f0 0%,#f0e8f8 100%);
    padding:6px 20px;font-family:'Tiro Bangla',serif;font-size:12px;color:#555;display:flex;justify-content:center;
}

.dc-header { background:#fff;border-bottom:2px solid #1a6b2e;padding:12px 24px 10px;display:flex;align-items:center;justify-content:space-between; }
.dc-header-left { display:flex;align-items:center;gap:12px; }
.dc-logo { width:52px;height:52px;border-radius:50%;background:#1a6b2e;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:12px;font-family:'Tiro Bangla',serif;flex-shrink:0; }
.dc-clinic-bn { font-family:'Tiro Bangla',serif;font-size:30px;font-weight:900;color:#1a6b2e;line-height:1; }
.dc-clinic-addr { font-family:'Tiro Bangla',serif;font-size:13px;color:#444;margin-top:3px; }
.dc-phones { font-size:11px;color:#666;margin-top:3px;line-height:1.7;text-align:right; }
.dc-title-banner { background:#1a6b2e;color:#fff;font-family:'Tiro Bangla',serif;font-size:26px;font-weight:900;text-align:center;padding:8px 0;letter-spacing:1px;border-bottom:2px solid #145424; }
.dc-ward-row { display:flex;justify-content:flex-end;gap:40px;padding:6px 28px;background:#f5faf5;border-bottom:1px solid #b5d9b5;font-size:12px;color:#333; }
.dc-ward-row span { display:flex;align-items:center;gap:4px; }
.dc-ward-row strong { color:#1a6b2e; }
.dc-body { padding:18px 30px 20px; }
.dc-field-row { display:flex;align-items:flex-end;gap:0;padding:9px 0;border-bottom:1px solid #ddd;font-size:13px;color:#222;font-family:'Tiro Bangla',serif;flex-wrap:wrap; }
.dc-field-row .dc-label { font-weight:700;color:#1a6b2e;white-space:nowrap;min-width:120px;flex-shrink:0; }
.dc-field-row .dc-val { flex:1;border-bottom:1px solid #aaa;min-height:20px;padding:0 4px;font-size:13px;color:#111; }
.dc-field-row .dc-sep { font-weight:700;color:#1a6b2e;padding:0 10px;white-space:nowrap; }
.dc-multiline-row { padding:10px 0;border-bottom:1px solid #ddd;font-size:13px;font-family:'Tiro Bangla',serif; }
.dc-multiline-row .dc-label { font-weight:700;color:#1a6b2e;margin-bottom:6px;display:block; }
.dc-multiline-content { min-height:40px;border-bottom:1px solid #aaa;padding:4px;font-size:13px;color:#111; }
.dc-multiline-content ul { list-style:none;padding:0;margin:0; }
.dc-multiline-content ul li { padding:3px 0;border-bottom:1px dotted #ccc;display:flex;gap:6px;font-size:12.5px; }
.dc-multiline-content ul li::before { content:"•";color:#1a6b2e;font-weight:900; }
.dc-condition-row { padding:10px 0;border-bottom:1px solid #ddd;font-size:13px;font-family:'Tiro Bangla',serif; }
.dc-condition-row .dc-label { font-weight:700;color:#1a6b2e; }
.dc-condition-row .dc-val { border-bottom:1px solid #aaa;display:inline-block;min-width:200px; }
.dc-advice-box { margin-top:12px;border:1.5px solid #1a6b2e;border-radius:4px;overflow:hidden; }
.dc-advice-box-title { background:#1a6b2e;color:#fff;font-family:'Tiro Bangla',serif;font-size:14px;font-weight:700;padding:5px 14px; }
.dc-advice-box-body { padding:10px 14px; }
.dc-sig-row { display:flex;justify-content:space-between;align-items:flex-end;margin-top:20px;padding-top:10px; }
.dc-sig-block { text-align:center; }
.dc-sig-line { width:200px;border-top:1.5px solid #333;margin:0 auto 4px;padding-top:2px; }
.dc-sig-label { font-size:12px;color:#555;font-family:'Tiro Bangla',serif; }
.dc-date-box { border:1px solid #1a6b2e;border-radius:3px;padding:5px 14px;font-size:13px;font-family:'Tiro Bangla',serif; }
.dc-date-box strong { color:#1a6b2e; }
.dc-footer { background:#f0faf0;border-top:2px solid #1a6b2e;padding:6px 24px;font-size:11px;color:#666;text-align:center;font-family:'Tiro Bangla',serif; }

.print-toolbar { max-width:960px;margin:0 auto 30px;display:flex;justify-content:flex-end;gap:10px; }
.btn-print-all { background:#1a3c5e;color:#fff;border:none;padding:9px 24px;border-radius:3px;font-size:14px;cursor:pointer; }
.btn-print-all:hover { background:#0d2b46; }
.empty-note { color:#aaa;font-style:italic;font-size:12px;padding:4px 0; }

/* ── Wrapper helpers for centered inline-block headings ── */
.heading-center-wrap { text-align: center; display: block; }

@media print {
    .template-selector,.print-toolbar,.content-header,.breadcrumb,.sheet-tag { display:none!important; }
    body { background:#fff!important; }
    .rx-sheet { box-shadow:none;border:1px solid #333;margin:0; }
}
</style>
@stop

@section('content')

<div class="template-selector">
    <div class="row">
        <div class="col-md-8">
            <select id="template_select" class="form-control">
                <option value="">-- Select Template --</option>
                @foreach($templates as $temp)
                    <option value="{{ $temp->templateid }}">{{ $temp->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn-load" onclick="loadMenuData()">
                <i class="fas fa-file-medical-alt"></i> Load Menu Data
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     PRESCRIPTION 1 — Rx On Admission
     ══════════════════════════════════════ --}}
<div class="rx-sheet" id="rx_admission">
    <div class="sheet-tag">Prescription 1 — Rx On Admission</div>
    <div class="clinic-header-blue">
        <div class="logo-row">
            <div class="clinic-logo-c">P</div>
            <div>
                <div class="clinic-bn-title">প্রফেসর ক্লিনিক</div>
                <div class="clinic-bn-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                <div class="clinic-phones">☎ 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
            </div>
        </div>
    </div>
    <div class="rx-patient-row">
        <span><strong>নাম :</strong> <span class="pt-name">—</span></span>
        <span><strong>বয়স :</strong> <span class="pt-age">—</span></span>
        <span><strong>তারিখ :</strong> {{ date('d-m-y') }}</span>
    </div>
    <div class="rx-admission-body">
        <div class="rx-col">
            {{-- O/E: inline-block so border stops at text edge --}}
            <div><span class="sec-label">O/E</span></div>
            <ul class="blist">
                <li>Pulse —</li>
                <li>BP —</li>
            </ul>
            {{-- Adv: inline-block so border stops at text edge --}}
            <div style="margin-top:14px;"><span class="sec-label">Adv</span></div>
            <ul class="blist" id="adm_inv_list"><li class="empty-note">Loading...</li></ul>
            <div style="margin-top:14px;">
                <div style="font-size:12px;font-weight:700;color:#1a3c5e;">Baby Note</div>
                <ul style="list-style:none;padding:0;margin:4px 0 0;">
                    <li style="font-size:12px;padding:3px 0;border-bottom:1px dotted #ddd;">Sex — Male / Female</li>
                    <li style="font-size:12px;padding:3px 0;border-bottom:1px dotted #ddd;">Weight — &nbsp;&nbsp;&nbsp; Kg</li>
                    <li style="font-size:12px;padding:3px 0;">Time — &nbsp;&nbsp;&nbsp; am/pm</li>
                </ul>
            </div>
        </div>
        <div class="rx-col">

            {{-- Rx On Admission On: centered, border only as wide as text --}}
            <div class="heading-center-wrap" style="margin-bottom:12px;">
                <span class="rx-on-adm-title">Rx On Admission On</span>
            </div>

            {{-- ADMIT medicines --}}
            <ul class="blist" id="adm_admit_meds"><li class="empty-note">Loading...</li></ul>

            {{-- Pre-Operative Order: centered, border only as wide as text --}}
            <div class="heading-center-wrap" style="margin: 16px 0 8px;">
                <span class="preop-plain-title">Pre-Operative Order</span>
            </div>

            {{-- PREORDER medicines - Hidden --}}
            <ul id="adm_preop_meds" style="list-style:none;padding:0;margin:0;display:none;">
                <li class="empty-note">No pre-order medicines</li>
            </ul>

            <ul style="list-style:none;padding:0;margin-top:8px;">
                <li style="font-size:12px;padding:4px 0;border-bottom:1px dotted #ddd;display:flex;gap:6px;flex-wrap:wrap;">
                    <span style="color:#1a3c5e;font-size:15px;font-weight:900;">•</span>
                    Plz take written &amp; informed consent from the patient or attendants
                </li>
                <li style="font-size:12px;padding:4px 0;border-bottom:1px dotted #ddd;display:flex;gap:6px;">
                    <span style="color:#1a3c5e;font-size:15px;font-weight:900;">•</span>
                    Plz clean &amp; shave the operative area.
                </li>
                <li style="font-size:12px;padding:4px 0;display:flex;gap:6px;flex-wrap:wrap;">
                    <span style="color:#1a3c5e;font-size:15px;font-weight:900;">•</span>
                    Plz. Send the patient to O.T at ..........
                    <span style="color:#c0392b;">dt. ——</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     PRESCRIPTION 2 — Post-Operative Order
     ══════════════════════════════════════ --}}
<div class="rx-sheet" id="rx_postop">
    <div class="sheet-tag">Prescription 2 — Post-Operative Order</div>
    <div class="clinic-header-blue">
        <div class="logo-row">
            <div class="clinic-logo-c">P</div>
            <div>
                <div class="clinic-bn-title">প্রফেসর ক্লিনিক</div>
                <div class="clinic-bn-sub">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                <div class="clinic-phones">☎ 01720-039005, 01720-039006, 01720-039007, 01720-039008</div>
            </div>
        </div>
    </div>
    <div class="rx-patient-row">
        <span><strong>নাম :</strong> <span class="pt-name">—</span></span>
        <span><strong>বয়স :</strong> <span class="pt-age">—</span></span>
        <span><strong>তারিখ :</strong> {{ date('d-m-y') }}</span>
    </div>

    <div class="post-op-body">

        {{-- Post-Operative Order On heading — full width, centered text --}}
        <div style="text-align:center; margin-bottom:18px;">
            <span class="post-order-plain">Post-Operative Order On</span>
        </div>

        {{-- medicines block centered horizontally --}}
        <div style="display:flex; justify-content:center;">
            <div style="width:460px; max-width:100%;">

                {{-- NPO-TFO --}}
                <ul class="blist">
                    <li>NPO-TFO</li>
                </ul>

                {{-- PREORDER medicines in Post-Operative section --}}
                <ul class="blist" id="pop_med_list" style="margin-top:0;">
                    <li class="empty-note">Loading pre-operative medicines...</li>
                </ul>

                {{-- Fixed footer items --}}
                <ul class="blist" style="margin-top:10px;">
                    <li>Continuous Catheterization</li>
                    <li>Plz record pulse/BP/Tem/U-O routinely.</li>
                </ul>

            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════
     PRESCRIPTION 3 — Doctor Pad
     ══════════════════════════════════════ --}}
<div class="rx-sheet" id="rx_pad">
    <div class="sheet-tag">Prescription 3 — Doctor Pad</div>
    <div class="clinic-header-pad">
        <div class="pad-clinic-side">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="clinic-logo-c" style="background:#1a5c35;width:50px;height:50px;">P</div>
                <div>
                    <div class="pad-clinic-name">প্রফেসর ক্লিনিক</div>
                    <div class="pad-clinic-addr">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
                    <div class="pad-clinic-phones">মোবাঃ 01720-039005, 01720-039006<br>01720-039007, 01720-039008</div>
                </div>
            </div>
        </div>
        <div class="pad-doctor-side">
            <div class="pad-doctor-name">ডাঃ জান্নাতুল ফেরদৌসী জুথি</div>
            <div class="pad-doctor-deg">এম.বি.বি.এস; বি.সি.এস (স্বাস্থ্য)<br>ডিপ্লোমা ইন মেডিকেল আল্ট্রাসাউড</div>
            <div class="pad-doctor-inst">শহীদ জিয়াউর রহমান মেডিকেল কলেজ, বগুড়া।</div>
        </div>
    </div>
    <div class="pad-name-row">
        <div class="field"><label>Name:</label><div class="val pt-name">—</div></div>
        <div class="field"><label>Age:</label><div class="val pt-age">—</div></div>
        <div class="field"><label>Date:</label><div class="val">{{ date('d-m-Y') }}</div></div>
    </div>
    <div class="pad-cc-row">C/C &nbsp; <span id="pad_cc_text" style="font-weight:400;color:#333;"></span></div>
    <div class="pad-body">
        <div class="pad-left">
            {{-- pad-sec-label also has display:inline-block in CSS --}}
            <div><span class="pad-sec-label">O/E</span></div>
            <ul class="pad-left-list">
                <li>Pulse</li><li>BP</li><li>Anaemia</li><li>Jaundice</li>
                <li>Tem</li><li>Oedema</li><li>Weight</li><li>Heart</li>
                <li>Lungs</li><li>FM</li>
            </ul>
            <div><span class="pad-sec-label">Inv</span></div>
            <ul class="pad-left-list" id="pad_inv_list"><li class="empty-note">Loading...</li></ul>
        </div>
        <div class="pad-right">
            <div class="rx-symbol">R<sub style="font-size:18px;">x</sub></div>
            <div style="min-height:30px;margin-bottom:8px;color:#555;font-size:13px;font-style:italic;" id="pad_complain_area"></div>
            <table class="pad-med-table" id="pad_med_table">
                <thead>
                    <tr>
                        <th rowspan="2" class="med-name-col">ওষুধের নাম</th>
                        <th colspan="3">কখন খাবেন?</th>
                        <th colspan="2">আহারের</th>
                        <th colspan="3">কতদিন</th>
                    </tr>
                    <tr>
                        <th class="sub">সকাল</th>
                        <th class="sub">দুপুর</th>
                        <th class="sub">রাত</th>
                        <th class="sub">আগে</th>
                        <th class="sub">পরে</th>
                        <th class="sub">দিন</th>
                        <th class="sub">মাস</th>
                        <th class="sub">চলবে</th>
                    </tr>
                </thead>
                <tbody id="pad_med_list">
                    <tr><td colspan="9" class="empty-note">Loading medicines...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="pad-footer">বিঃ দ্রঃ ......................................দিন/মাস পর ব্যবস্থাপত্র-সহ সাক্ষাৎ করিবেন।</div>
</div>

{{-- ══════════════════════════════════════
     PRESCRIPTION 4 — Discharge + Op Note + Advice
     ══════════════════════════════════════ --}}
<div class="rx-sheet" id="rx_discharge">
    <div class="sheet-tag">Prescription 4 — ছাড় পত্র / অপারেশন নোট / পরামর্শ</div>
    <div class="dc-header">
        <div class="dc-header-left">
            <div class="dc-logo">P</div>
            <div>
                <div class="dc-clinic-bn">প্রফেসর ক্লিনিক</div>
                <div class="dc-clinic-addr">মাঝিড়া, শাজাহানপুর, বগুড়া।</div>
            </div>
        </div>
        <div class="dc-phones">☎ 01720-039005<br>☎ 01720-039006<br>☎ 01720-039007<br>☎ 01720-039008</div>
    </div>
    <div class="dc-title-banner">ছাড় পত্র</div>
    <div class="dc-ward-row">
        <span><strong>ওয়ার্ড/কেবিন নং-</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span><strong>রেজিং নং-</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    </div>
    <div class="dc-body">
        <div class="dc-field-row">
            <span class="dc-label">প্রত্যয়ন করা যাচ্ছে</span>
            <span class="dc-val pt-name" style="font-size:14px;font-weight:600;">—</span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label">বয়স</span>
            <span class="dc-val pt-age" style="max-width:80px;">—</span>
            <span class="dc-sep">পিতা/স্বামী</span>
            <span class="dc-val" style="flex:2;">—</span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label">ঠিকানা ঃ গ্রাম</span>
            <span class="dc-val">—</span>
            <span class="dc-sep">পোষ্ট</span>
            <span class="dc-val">—</span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label" style="min-width:80px;">থানা</span>
            <span class="dc-val">—</span>
            <span class="dc-sep">জেলা</span>
            <span class="dc-val">—</span>
            <span class="dc-sep">তারিখ</span>
            <span class="dc-val" style="max-width:100px;">{{ date('d-m-Y') }}</span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label">অত্র হাসপাতালে</span>
            <span class="dc-val" style="max-width:180px;">—</span>
            <span class="dc-sep">তারিখ হতে</span>
            <span class="dc-val" style="max-width:110px;">—</span>
            <span class="dc-sep">তারিখ</span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label">পর্যন্ত চিকিৎসাধীন ছিলেন। তিনি</span>
            <span class="dc-val" id="dc_complaint_text">—</span>
        </div>
        <div class="dc-field-row" style="border-bottom:1px solid #ddd;">
            <span style="font-size:13px;font-family:'Tiro Bangla',serif;color:#222;flex:1;">
                রোগে ভুগছিলেন। তিনি নিম্নোক্ত চিকিৎসকের অধীনে চিকিৎসাধীন ছিলেন।
            </span>
        </div>
        <div class="dc-field-row">
            <span class="dc-label">চিকিৎসক</span>
            <span class="dc-val" style="font-size:13px;font-weight:600;"></span>
        </div>
        <div class="dc-multiline-row" style="margin-top:4px;">
            <span class="dc-label">রোগ নির্ণয় (Diagnosis)</span>
            <div class="dc-multiline-content">
                <ul id="dc_diag_list"><li class="empty-note">Loading...</li></ul>
            </div>
        </div>
        <div class="dc-advice-box" style="margin-top:14px;">
            <div class="dc-advice-box-title">প্রদত্ত চিকিৎসা পরামর্শ (Discharge Advice &amp; Medicines)</div>
            <div class="dc-advice-box-body">
                <ul class="blist" id="dc_med_list" style="margin-bottom:8px;"><li class="empty-note">Loading medicines...</li></ul>
                <div style="font-size:12px;font-weight:700;color:#1a6b2e;border-bottom:1.5px solid #1a6b2e;padding-bottom:2px;margin:10px 0 6px;text-transform:uppercase;display:inline-block;">পরামর্শ (Advice)</div>
                <ul class="blist" id="dc_advice_list"><li class="empty-note">Loading...</li></ul>
                <div style="font-size:12px;font-weight:700;color:#1a6b2e;border-bottom:1.5px solid #1a6b2e;padding-bottom:2px;margin:10px 0 6px;text-transform:uppercase;display:inline-block;">পরীক্ষা নিরীক্ষা (Investigations)</div>
                <ul class="blist" id="dc_inv_list"><li class="empty-note">Loading...</li></ul>
            </div>
        </div>
        <div class="dc-condition-row" style="margin-top:12px;">
            <span class="dc-label">অবস্থা :</span>
            <span class="dc-val">Improved / Stable / referred / LAMA / Died</span>
        </div>
        <div class="dc-sig-row">
            <div class="dc-date-box"><strong>তাং-</strong> {{ date('d-m-Y') }}</div>
            <div class="dc-sig-block">
                <div class="dc-sig-line"></div>
                <div class="dc-sig-label">কর্তব্যরত চিকিৎসকের স্বাক্ষর</div>
            </div>
            <div class="dc-sig-block">
                <div class="dc-sig-line"></div>
                <div class="dc-sig-label">স্বাক্ষর</div>
            </div>
        </div>
    </div>

    <div style="border-top:3px solid #1a6b2e;">
        <div class="op-note-title-banner" style="margin-top:16px;">অপারেশন নোট</div>
        <div class="op-note-body">
            <div class="op-row"><div class="op-row-label">অপারেশন<br>নাম ও তাং</div><div class="op-row-value"></div></div>
            <div class="op-row tall"><div class="op-row-label">অপারেশন পদ্ধতি<br>ও<br>প্রাপ্ত তথ্যাবলী<br>নাম ও তাং</div><div class="op-row-value"></div></div>
            <div class="op-row"><div class="op-row-label">সার্জন</div><div class="op-row-value"></div></div>
            <div class="op-row"><div class="op-row-label">এ্যানেসথেটিস্ট</div><div class="op-row-value"></div></div>
            <div class="op-row"><div class="op-row-label">এ্যাসিসট্যান্ট</div><div class="op-row-value"></div></div>
            <div class="op-row"><div class="op-row-label">এ্যাসিসট্যান্ট</div><div class="op-row-value"></div></div>
        </div>
    </div>

    <div style="border-top:3px solid #1a6b2e;">
        <div class="pdp-title-banner" style="margin-top:16px;">প্রদত্ত চিকিৎসা পরামর্শ</div>
        <div class="pdp-big-area">
            <ul class="blist" id="pdp_med_list"><li class="empty-note">Loading...</li></ul>
            <ul class="blist" id="pdp_advice_list" style="margin-top:8px;"><li class="empty-note">Loading...</li></ul>
        </div>
        <div class="pdp-sub-banner">পরীক্ষা নিরীক্ষা</div>
        <div class="pdp-inv-area">
            <ul class="blist" id="pdp_inv_list"><li class="empty-note">Loading...</li></ul>
        </div>
        <div class="pdp-footer-row">
            <span>রক্ত গ্রুপ<span style="letter-spacing:3px;">...............</span></span>
            <span>ড্রাগ এ্যালার্জী<span style="letter-spacing:3px;">...............</span></span>
        </div>
    </div>

    <div style="border-top:3px solid #1a6b2e;">
        <div class="cs-title-banner" style="margin-top:16px;">চিকিৎসা পরামর্শ ও উপদেশাবলী</div>
        <div class="cs-advice-area">
            <ul class="blist" id="cs_advice_list"><li class="empty-note">Loading...</li></ul>
        </div>
        <div class="cs-special-banner">ক্লিনিকের বিশেষ আকর্ষণ ঃ</div>
        <ul class="cs-attraction-list">
            <li>২৪ ঘন্টা ইমার্জেন্সী সার্ভিস প্রদান।</li>
            <li>দিবা-রাত্রি সব সময় সিজারসহ যেকোন অপারেশনের সু-ব্যবস্থা।</li>
            <li>মহিলা এম,বি,বি,এস ডাক্তার দ্বারা নরমাল ডেলিভারির সু-ব্যবস্থা।</li>
            <li>গরীব ও দুঃস্থ রোগীদের সুবিধার্থে প্রতি সোমবার বিকেলে ফ্রি প্রেসক্রিপশন প্রদান।</li>
            <li>অন্তঃসত্ত্বা মহিলাদের জন্য চেক-আপের বিশেষ সুবিধাদি প্রদান।</li>
        </ul>
    </div>

    <div class="dc-footer">
        বিঃদ্রঃ ......................................দিন/মাস পর ব্যবস্থাপত্র-সহ সাক্ষাৎ করিবেন।
    </div>
</div>

<div class="print-toolbar" id="print_toolbar" style="display:none;">
    <button class="btn-print-all" onclick="window.print()">
        <i class="fas fa-print"></i> Print All 4 Prescriptions
    </button>
</div>

@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function parseMedicineDose(m) {
    let morning = '', noon = '', night = '';

    if (m.morning !== undefined && m.morning !== null && m.morning !== '') morning = String(m.morning);
    if (m.noon    !== undefined && m.noon    !== null && m.noon    !== '') noon    = String(m.noon);
    if (m.night   !== undefined && m.night   !== null && m.night   !== '') night   = String(m.night);

    if (!morning && !noon && !night) {
        const doseStr = m.dose || m.dosage || '';
        if (doseStr) {
            const parts = doseStr.split('+');
            morning = parts[0] ? parts[0].trim() : '';
            noon    = parts[1] ? parts[1].trim() : '';
            night   = parts[2] ? parts[2].trim() : '';
        }
    }

    const mealRaw  = (m.meal_timing || m.instruction || '').toLowerCase();
    const isBefore = mealRaw.includes('before') || mealRaw === 'before';
    const isAfter  = mealRaw.includes('after')  || mealRaw === 'after';

    const durStr   = (m.duration || '').trim();
    let durDin = '', durMas = '', durCholbe = '';
    const durLower = durStr.toLowerCase();

    if (durLower === 'চলবে' || durLower === 'continue' || durLower === 'ongoing' ||
        durLower.includes('চলবে') || durLower.includes('continue')) {
        durCholbe = '✓';
    } else {
        const parts = durStr.split(/\s+/);
        const num   = parts[0] || '';
        const unit  = (parts[1] || '').toLowerCase();
        if (unit.includes('মাস') || unit.includes('month')) { durMas = num; }
        else if (num) { durDin = num; }
    }

    return { morning, noon, night, isBefore, isAfter, durDin, durMas, durCholbe };
}

function buildPadMedTable(meds) {
    if (!meds || !meds.length) {
        return '<tr><td colspan="9" class="empty-note" style="text-align:center;padding:12px;">No medicines found</td></tr>';
    }
    let h = '';
    meds.forEach(function(m) {
        const name    = m.name || m.brand || '—';
        const p       = parseMedicineDose(m);
        const route   = m.route   ? `<span class="med-dose">${m.route}</span>`   : '';
        const company = m.company ? `<span class="med-dose">${m.company}</span>` : '';
        const extras  = [route, company].filter(Boolean).join(' ');
        h += `<tr>
            <td class="med-name">${name}${extras ? '<br>' + extras : ''}</td>
            <td class="chk">${p.morning || ''}</td>
            <td class="chk">${p.noon    || ''}</td>
            <td class="chk">${p.night   || ''}</td>
            <td class="chk-mark">${p.isBefore ? '✓' : ''}</td>
            <td class="chk-mark">${p.isAfter  ? '✓' : ''}</td>
            <td class="chk">${p.durDin   || ''}</td>
            <td class="chk">${p.durMas   || ''}</td>
            <td class="chk-mark">${p.durCholbe || ''}</td>
        </tr>`;
    });
    return h;
}

function buildMedHtml(meds, emptyMsg) {
    if (!meds || !meds.length) {
        return `<li class="empty-note">${emptyMsg || 'No medicines found'}</li>`;
    }
    let h = '';
    meds.forEach(function(m) {
        const name = m.name || m.brand || '—';
        const p    = parseMedicineDose(m);

        let doseParts = [];
        if (p.morning) doseParts.push('সকাল:' + p.morning);
        if (p.noon)    doseParts.push('দুপুর:' + p.noon);
        if (p.night)   doseParts.push('রাত:'   + p.night);

        let mealLabel = '';
        if (p.isBefore)     mealLabel = 'আহারের আগে';
        else if (p.isAfter) mealLabel = 'আহারের পরে';

        let durLabel = '';
        if (p.durCholbe)   durLabel = 'চলবে';
        else if (p.durMas) durLabel = p.durMas + ' মাস';
        else if (p.durDin) durLabel = p.durDin + ' দিন';

        const subParts = [doseParts.join('  '), mealLabel, durLabel].filter(Boolean);
        const sub = subParts.length
            ? `<span class="med-sub">${subParts.join(' &nbsp;|&nbsp; ')}</span>` : '';

        h += `<li class="red">${name}${sub}</li>`;
    });
    return h;
}

function buildPreOpHtml(meds) {
    if (!meds || !meds.length) {
        return '<li style="font-size:12px;color:#aaa;font-style:italic;">No pre-order medicines</li>';
    }
    let h = '';
    meds.forEach(function(m) {
        const name  = m.name || m.brand || '—';
        const p     = parseMedicineDose(m);
        let doseStr = [p.morning, p.noon, p.night].filter(Boolean).join('+');
        const sub   = (doseStr || m.duration)
            ? `<span class="med-sub">${[doseStr, m.duration].filter(Boolean).join(' — ')}</span>` : '';
        h += `<li style="font-size:12px;padding:4px 0;border-bottom:1px dotted #ddd;display:flex;gap:6px;flex-wrap:wrap;">
                <span style="color:#1a3c5e;font-size:15px;font-weight:900;">•</span>
                <span>${name}${sub}</span>
              </li>`;
    });
    return h;
}

function loadMenuData() {
    let templateid = $('#template_select').val();
    if (!templateid) { alert('Please select a template'); return; }

    $('.rx-sheet').removeClass('visible');
    $('#print_toolbar').hide();

    const loading = '<li class="empty-note">Loading...</li>';
    $('#adm_admit_meds,#adm_preop_meds,#adm_inv_list').html(loading);
    $('#pop_med_list,#dc_diag_list,#dc_med_list,#dc_advice_list,#dc_inv_list').html(loading);
    $('#pdp_med_list,#pdp_advice_list,#pdp_inv_list,#cs_advice_list').html(loading);
    $('#pad_inv_list').html(loading);
    $('#pad_med_list').html('<tr><td colspan="9" class="empty-note">Loading medicines...</td></tr>');
    $('#pad_cc_text,#pad_complain_area').text('');
    $('#dc_complaint_text').text('—');

    loadMedicines(templateid);
    loadDiagnosis(templateid);
    loadInvestigations(templateid);
    loadAdvice(templateid);
    loadComplains(templateid);
    loadDischarge(templateid);
}

function loadMedicines(templateid) {
    $.ajax({
        url:  "{{ route('templates.medicine.ajax.list') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) {
            if (res.rows && res.rows.length > 0) {
                const rows       = res.rows;
                const admitMeds  = rows.filter(r => r.order_type === 'admit');
                const preordMeds = rows.filter(r => r.order_type === 'preorder');
                const postMeds   = rows.filter(r => r.order_type === 'postorder');

                $('#adm_admit_meds').html(buildMedHtml(admitMeds, 'No admission medicines'));
                // Hide pre-operative medicines - only show fixed text
                $('#adm_preop_meds').hide();
                // Show pre-operative medicines in Post-Operative section
                $('#pop_med_list').html(buildMedHtml(preordMeds, 'No pre-operative medicines'));
                // Show only fresh prescription medicines (AT Admission) in Doctor Pad
                $('#pad_med_list').html(buildPadMedTable(admitMeds));
                $('#dc_med_list').html(buildMedHtml(rows));
                $('#pdp_med_list').html(buildMedHtml(rows));

            } else {
                const e  = '<li class="empty-note">No medicines found</li>';
                const et = '<tr><td colspan="9" class="empty-note" style="text-align:center;padding:10px;">No fresh prescription medicines found</td></tr>';
                $('#adm_admit_meds,#pop_med_list,#dc_med_list,#pdp_med_list').html(e);
                $('#adm_preop_meds').hide();
                $('#pad_med_list').html(et);
            }
            showAllSheets();
        },
        error: function() {
            $('#adm_admit_meds').html('<li class="empty-note">Error loading medicines</li>');
            showAllSheets();
        }
    });
}

function loadDiagnosis(templateid) {
    $.ajax({
        url: "{{ route('templates.diagnosis.ajax.list') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) {
            let html = '';
            if (res.rows && res.rows.length > 0) {
                res.rows.forEach(item => {
                    html += `<li><strong>${item.name}</strong>${item.note ? ' — ' + item.note : ''}</li>`;
                });
            } else { html = '<li class="empty-note">No diagnosis found</li>'; }
            $('#dc_diag_list').html(html);
        }
    });
}

function loadInvestigations(templateid) {
    $.ajax({
        url: "{{ route('templates.investigation.ajax.list') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) {
            let html = '';
            if (res.rows && res.rows.length > 0) {
                res.rows.forEach(item => {
                    html += `<li><strong>${item.name}</strong>${item.note ? ' — ' + item.note : ''}</li>`;
                });
            } else { html = '<li class="empty-note">No investigations found</li>'; }
            $('#adm_inv_list,#pad_inv_list,#dc_inv_list,#pdp_inv_list').html(html);
        }
    });
}

function loadAdvice(templateid) {
    $.ajax({
        url: "{{ route('templates.advice.ajax.list') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) {
            let html = '';
            if (res.rows && res.rows.length > 0) {
                res.rows.forEach(item => { html += `<li>${item.advice}</li>`; });
            } else { html = '<li class="empty-note">No advice found</li>'; }
            $('#dc_advice_list,#pdp_advice_list,#cs_advice_list').html(html);
        }
    });
}

function loadComplains(templateid) {
    $.ajax({
        url: "{{ route('templates.complain.ajax.list') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) {
            if (res.rows && res.rows.length > 0) {
                const ccText = res.rows.map(c => c.name).join(' | ');
                $('#pad_cc_text').text(ccText);
                $('#pad_complain_area').text(res.rows.map(c => c.name + (c.note ? ' — ' + c.note : '')).join('; '));
                $('#dc_complaint_text').text(ccText);
            }
        }
    });
}

function loadDischarge(templateid) {
    $.ajax({
        url: "{{ route('templates.discharge.ajax.get') }}",
        type: "GET",
        data: { templateid: templateid },
        success: function(res) { /* extend if needed */ },
        error: function() { /* silent */ }
    });
}

function showAllSheets() {
    $('#rx_admission,#rx_postop,#rx_pad,#rx_discharge').addClass('visible');
    $('#print_toolbar').show();
    $('html,body').animate({ scrollTop: $('#rx_admission').offset().top - 15 }, 350);
}
</script>
@stop