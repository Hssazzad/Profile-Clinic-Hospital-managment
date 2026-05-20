{{-- resources/views/prescriptions/pdf.blade.php --}}
@php
    $pid = $pid ?? $rx->id ?? request('id');
@endphp

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style>
  body{
    font-family: solaimanlipi, sans-serif;
    font-size:12px;
    color:#111;
    line-height:1.45;
  }

  /* Force Bangla font on headings */
  b,h1, h2, h3, h4, h5, h6{
    font-family: solaimanlipi, sans-serif !important;
    font-weight: bold;
  }

  .header{
    border-bottom:1px solid #ccc;
    padding-bottom:8px;
    margin-bottom:12px;
  }

  .row{ width:100%; margin-bottom:6px; }
  .col{ width:49%; display:inline-block; vertical-align:top; }

  .box{
    border:1px solid #ddd;
    padding:8px;
    margin-bottom:10px;
    border-radius:6px;
  }

  h2{ margin:0; padding:0; font-size:18px; }
  h3{ margin:0 0 8px 0; font-size:14px; }

  table{
    font-family: solaimanlipi, sans-serif; /* ✅ IMPORTANT: same font */
    width:100%;
    border-collapse:collapse;
    margin-top:5px;
  }

  th,td{
	font-family: solaimanlipi, sans-serif; /* ✅ IMPORTANT: same font */
    border:1px solid #ddd;
    padding:5px;
    vertical-align: top;
  }

  th{
	font-family: solaimanlipi, sans-serif; /* ✅ IMPORTANT: same font */
    background:#f3f3f3;
    text-align:left;
  }

  ul{
    margin:4px 0 0;
    padding-left:16px;
  }

  .sign{
    margin-top:20px;
  }

  .line{
    border-top:1px solid #333;
    width:220px;
    display:inline-block;
    text-align:center;
    padding-top:4px;
    margin-top:20px;
  }
</style>

{{--======================
    HEADER
=======================--}}
<div class="header">
  <div class="row">
    <div class="col">
      <h2>প্রেসক্রিপশন</h2>
      <div style="font-size:16px;">বাংলা টেস্ট ঠিক আছে কি?</div>

      <div>
        ডাক্তার
        {{ $rx->doctor_name ?? '—' }}
        ({{ $rx->doctor_reg_no ?? '—' }})
      </div>

      <div>
        তারিখ:
        {{ $rx->prescribed_on ?? ($rx->created_at ?? now()->toDateString()) }}
      </div>
    </div>

    <div class="col" style="text-align:right;">
      <div>প্রেসক্রিপশন নং: {{ $pid ?? '—' }}</div>
      <div>রোগী আইডি: {{ $patient->patientcode ?? '—' }}</div>
    </div>
  </div>
</div>


{{--======================
   PATIENT INFO
=======================--}}
<div class="box">
  <h3>রোগীর তথ্য</h3>

  <div class="row">
    <div class="col">
      <div>নাম:   {{ $patient->patientname ?? '—' }}</div>
      <div>লিঙ্গ:  {{ $patient->gender ?? '—' }}</div>
      <div>বয়স: {{ $patient->age ?? '—' }}</div>
      <div>রক্তের গ্রুপ: {{ $patient->blood_group ?? '—' }}</div>
    </div>

    <div class="col">
      <div>মোবাইল: {{ $patient->mobile_no ?? '—' }}</div>
      <div>এনআইডি: {{ $patient->nid_number ?? '—' }}</div>
      <div>ঠিকানা: {{ $patient->address ?? '—' }}</div>
    </div>
  </div>
</div>


{{--======================
   COMPLAINTS + DIAGNOSIS
=======================--}}
<div class="box">
  অভিযোগ ও রোগ নির্ণয়

  <div class="row">
    <div class="col">
      প্রধান অভিযোগ:<br>

      @php $ccText = $rx->chief_complaint ?? ''; @endphp

      @if($ccText)
        {{ $ccText }}
      @elseif(($complaints ?? collect())->count())
        <ul>
          @foreach($complaints as $c)
            <li>{{ $c->complaint }}@if($c->note) — {{ $c->note }}@endif</li>
          @endforeach
        </ul>
      @else
        —
      @endif
    </div>

    <div class="col">
      রোগ নির্ণয়:<br>

      @php $diagText = $rx->diagnosis ?? ''; @endphp

      @if($diagText)
        {{ $diagText }}
      @elseif(($diagnoses ?? collect())->count())
        <ul>
          @foreach($diagnoses as $d)
            <li>{{ $d->name }}@if($d->note) — {{ $d->note }}@endif</li>
          @endforeach
        </ul>
      @else
        —
      @endif
    </div>
  </div>

  @if(!empty($rx?->advice))
    <div class="row" style="margin-top:6px">
      পরামর্শ:<br>
      {{ $rx->advice }}
    </div>
  @endif
</div>


{{--======================
   INVESTIGATIONS
=======================--}}
<div class="box">
  পরীক্ষা-নিরীক্ষা

  @if(($investigations ?? collect())->count())
    <table>
      <thead>
        <tr>
          <th style="width:40%">পরীক্ষার নাম</th>
          <th>মন্তব্য</th>
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
    কোনো পরীক্ষা নির্ধারিত নেই
  @endif
</div>


{{--======================
   MEDICINE LIST
=======================--}}
<div class="box">
  <h3> কোনো ওষুধ দেওয়া হয়নি</h3> 

  @if(($medicines ?? collect())->count())
    <table>
      <thead>
        <tr>
          <th style="width:28%">ওষুধের নাম</th>
          <th>শক্তি</th> {{-- ✅ you had wrong label here --}}
          <th>মাত্রা</th>
          <th>পথ</th>
          <th>বার/দিন</th>
          <th>সময়কাল</th>
          <th>খাওয়ার নিয়ম</th>
        </tr>
      </thead>

      <tbody>
        @foreach($medicines as $m)
          <tr>
            <td>{{ $m->name }}</td>
            <td>{{ $m->strength }}</td>
            <td>{{ $m->dose }}</td>
            <td>{{ $m->route }}</td>
            <td>{{ $m->frequency }}</td>
            <td>{{ $m->duration }}</td>
            <td>{{ $m->timing }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    কোনো ওষুধ দেওয়া হয়নি
  @endif
</div>


{{--======================
   SIGNATURE
=======================--}}
<div class="sign">
  <div class="line">রোগীর স্বাক্ষর</div>
  <div class="line" style="float:right;">ডাক্তারের স্বাক্ষর</div>
</div>
