{{-- resources/views/prescriptions/tabs/preview.blade.php --}}
@php
  $pid       = $pid ?? request('id');
  $patientId = $patientId ?? request('patient');

  $clinicName  = config('app.name', 'ProfClinic');
  $clinicAddr  = $clinicAddr ?? 'আপনার ক্লিনিকের ঠিকানা এখানে দিন';
  $clinicPhone = $clinicPhone ?? 'মোবাইল: 01XXXXXXXXX';
@endphp

{{-- Bangla font for HTML preview + printing --}}
<style>
  @font-face {
    font-family: 'notobengali';
    font-weight: normal;
    font-style: normal;
    src: url("{{ asset('fonts/NotoSansBengali-Regular.ttf') }}") format('truetype');
  }
  @font-face {
    font-family: 'notobengali';
    font-weight: bold;
    font-style: normal;
    src: url("{{ asset('fonts/NotoSansBengali-Bold.ttf') }}") format('truetype');
  }
  body, *{
    font-family: 'notobengali', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif !important;
  }
</style>

<style>
  :root{
    --ink:#0f172a;
    --muted:#6b7280;
    --line:#e5e7eb;
    --soft:#f8fafc;
    --brand:#0ea5e9;
  }

  .rx-wrap{
    max-width: 920px;
    margin: 0 auto;
    padding: 12px;
    color: var(--ink);
  }

  /* ========= PRINTABLE AREA LAYOUT ========= */
  #printableArea{
    background:#fff;
    padding: 8px;
  }

  .print-header{
    display:flex;justify-content:space-between;align-items:flex-start;gap:12px;
    padding-bottom:10px;border-bottom:2px solid var(--line);margin-bottom:12px;
  }
  .clinic-left{display:flex;gap:10px;align-items:center;}
  .clinic-logo{
    width:54px;height:54px;border-radius:10px;background:var(--soft);
    border:1px solid var(--line);display:flex;align-items:center;justify-content:center;
    font-weight:700;color:var(--brand);
  }
  .clinic-title h2{margin:0;font-size:20px;line-height:1.2;}
  .clinic-title .sub{font-size:12px;color:var(--muted);}
  .clinic-right{font-size:12px;color:var(--muted);text-align:right;}

  .rx-top-strip{
    display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;margin-bottom:10px;
  }
  .strip-box{
    border:1px solid var(--line);border-radius:10px;padding:8px 10px;background:#fff;
  }

  .box{
    border:1px solid var(--line);border-radius:12px;padding:10px 12px;margin-bottom:10px;background:#fff;
  }
  .box h3{
    margin:0 0 8px 0;font-size:15px;padding-bottom:6px;border-bottom:1px solid var(--line);
  }

  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}

  table.rx{width:100%;border-collapse:collapse;font-size:12.5px;}
  table.rx th, table.rx td{border:1px solid var(--line);padding:6px 7px;vertical-align:top;}
  table.rx th{background:var(--soft);text-align:left;font-weight:700;}

  ul{ margin:4px 0 0; padding-left:18px; }

  .sign{display:flex;justify-content:space-between;gap:10px;margin-top:16px;}
  .sign .line{
    width:48%;border-top:1px solid #9ca3af;text-align:center;padding-top:4px;font-size:12px;color:var(--muted);
  }

  .print-footer{
    border-top:1px dashed var(--line);
    margin-top:12px;padding-top:8px;font-size:11px;color:var(--muted);
    display:flex;justify-content:space-between;align-items:center;
  }
  .page-no:after{content:"পৃষ্ঠা " counter(page) " / " counter(pages);}

  /* ========= SCREEN-ONLY UI ========= */
  .actions{
    display:flex;gap:.5rem;justify-content:space-between;margin-top:.75rem;flex-wrap:wrap;
  }
  .btn{
    padding:.5rem .9rem;border-radius:.6rem;border:1px solid #cfe1f4;
    background:var(--brand);color:#fff;cursor:pointer;text-decoration:none;display:inline-block;font-size:13px;
  }
  .btn.secondary{background:#fff;color:#0e1a2a;}
  .pdf-frame-wrap{
    margin-top:.75rem;border:1px solid var(--line);border-radius:.8rem;overflow:hidden;background:#fff;
  }
  #rxPdfFrame{width:100%;height:88vh;border:none;display:block;}

  /* ========= PRINT ONLY PRINTABLE AREA ========= */
  @page{ size:A4 portrait; margin:18mm 12mm; }

  @media print{
    body{ background:#fff !important; }
    body *{ visibility:hidden !important; }

    #printableArea, #printableArea *{
      visibility:visible !important;
    }
    #printableArea{
      position:absolute;
      left:0; top:0; right:0;
      width:100%;
      padding:0;
      margin:0;
    }

    .box{ page-break-inside: avoid; }
  }
</style>

<div class="rx-wrap">
  @if(!$pid)
    <div class="alert alert-warning">
      প্রেসক্রিপশন আইডি পাওয়া যায়নি। অনুগ্রহ করে রোগী সিলেক্ট করে প্রেসক্রিপশন তৈরি করে তারপর Preview ট্যাবে আসুন।
    </div>
  @else

  {{-- ===================== PRINTABLE ONLY ===================== --}}
  <div id="printableArea">

    {{-- Header --}}
    <div class="print-header">
      <div class="clinic-left">
        <div class="clinic-logo">Rx</div>
        <div class="clinic-title">
          <h2>{{ $clinicName }}</h2>
          <div class="sub">{{ $clinicAddr }}</div>
          <div class="sub">{{ $clinicPhone }}</div>
        </div>
      </div>

      <div class="clinic-right">
        <div><b>প্রেসক্রিপশন আইডি:</b> {{ $pid }}</div>
        <div><b>তারিখ:</b> {{ ($rx->prescribed_on ?? now()->toDateString()) }}</div>
        <div><b>রোগীর কোড:</b> {{ $patient->patientcode ?? '—' }}</div>
      </div>
    </div>

    {{-- Top strip --}}
    <div class="rx-top-strip">
      <div class="strip-box">
        <div><b>ডাক্তার:</b> {{ $rx->doctor_name ?? '—' }} ({{ $rx->doctor_reg_no ?? '—' }})</div>
        <div><b>বিভাগ/বিশেষত্ব:</b> {{ $rx->doctor_department ?? '—' }}</div>
      </div>

      <div class="strip-box">
        <div><b>রোগীর নাম:</b> {{ $patient->patientname ?? '—' }}</div>
        <div><b>বয়স/লিঙ্গ:</b> {{ $patient->age ?? '—' }} / {{ $patient->gender ?? '—' }}</div>
        <div><b>মোবাইল:</b> {{ $patient->mobile_no ?? '—' }}</div>
      </div>
    </div>

    {{-- Patient Full Info --}}


    {{-- Complaints + Diagnosis + Advice --}}
    <div class="box">
      <h3>উপসর্গ ও রোগ নির্ণয়</h3>
	  
      <div class="grid-2">
        {{-- Complaints --}}
        <div>
          <b>প্রধান অভিযোগ:</b>
          @php $ccText = $rx->chief_complaint ?? ''; @endphp

          @if($ccText)
            <div>{{ $ccText }}</div>
          @elseif(($complaints ?? collect())->count())
            <ul>
              @foreach($complaints as $c)
                <li>{{ $c->complaint }}@if($c->note) — <em>{{ $c->note }}</em>@endif</li>
              @endforeach
            </ul>
          @else
            <div>—</div>
          @endif
        </div>

        {{-- Diagnosis --}}
        <div>
          <b>রোগ নির্ণয়:</b>
          @php $diagText = $rx->diagnosis ?? ''; @endphp

          @if($diagText)
            <div>{{ $diagText }}</div>
          @elseif(($diagnoses ?? collect())->count())
            <ul>
              @foreach($diagnoses as $d)
                <li>{{ $d->name }}@if($d->note) — <em>{{ $d->note }}</em>@endif</li>
              @endforeach
            </ul>
          @else
            <div>—</div>
          @endif
        </div>
      </div>

      {{-- Advice --}}
      @if(!empty($rx?->advice))
        <div style="margin-top:8px">
          <b>পরামর্শ:</b><br>
          {{ $rx->advice }}
        </div>
      @endif
    </div>

    

    {{-- Medicines --}}
    <div class="box">
      <h3>ঔষধসমূহ</h3>
      @if(($medicines ?? collect())->count())
        <table class="rx">
          <thead>
            <tr>
              <th style="width:28%">Name of Medicine</th>
              <th>Medicine</th>
              <th>Dose</th>
              <th>ফ্রিকোয়েন্সি</th>
              <th>সময়কাল</th>
              <th>খাওয়ার নিয়ম</th>
            </tr>
          </thead>
          <tbody>
            @foreach($medicines as $m)
              <tr>
                <td>{{ $m->name }}</td>
                <td>{{ $m->strength }}</td>
                <td>{{ $m->dose }}</td>
               
                <td>{{ $m->frequency }}</td>
                <td>{{ $m->duration }}</td>
                <td>{{ $m->timing }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <em>কোনো ঔষধ যোগ করা হয়নি।</em>
      @endif
    </div>
    {{-- Investigations --}}
    <div class="box">
      <h3>পরীক্ষা-নিরীক্ষা</h3>
      @if(($investigations ?? collect())->count())
        <table class="rx">
          <thead>
            <tr>
              <th style="width:40%">পরীক্ষা</th>
              <th>নোট</th>
            </tr>
          </thead>
          <tbody>
            @foreach($investigations as $i)
              <tr>
                <td>{{ $i->name }}</td>
                <td>{{ $i->note }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <em>কোনো পরীক্ষা-নিরীক্ষা যোগ করা হয়নি।</em>
      @endif
    </div>
    {{-- Signature --}}
    <div class="sign">
      <div class="line">রোগী / অভিভাবক</div>
      <div class="line">ডাক্তারের স্বাক্ষর</div>
    </div>

    {{-- Footer --}}
    <div class="print-footer">
      <div>
        এই প্রেসক্রিপশনটি কম্পিউটার জেনারেটেড।<br>
        প্রয়োজনে যোগাযোগ করুন: {{ $clinicPhone }}
      </div>
      <div class="page-no"></div>
    </div>

  </div>
  {{-- ===================== /PRINTABLE ONLY ===================== --}}

  {{-- SCREEN-ONLY BUTTONS + TAB UI --}}
  <div class="actions">
    <a class="btn secondary"
       href="{{ route('rx.wizard',['id'=>$pid,'patient'=>$patientId,'tab'=>'medicine']) }}">
      ← ঔষধে ফিরে যান
    </a>

    <a class="btn secondary" target="_blank"
       href="{{ route('rx.pdf.inline',['id'=>$pid]) }}">
      Inline PDF খুলুন
    </a>

    <a class="btn" href="#" onclick="window.print();return false;">
      শুধু প্রেসক্রিপশন প্রিন্ট করুন
    </a>
  </div>

  <div class="pdf-frame-wrap">
    <iframe
      id="rxPdfFrame"
      src="{{ route('rx.pdf.inline',['id'=>$pid]) }}"
      loading="lazy"
      referrerpolicy="no-referrer"
    ></iframe>
  </div>

  @endif
</div>
