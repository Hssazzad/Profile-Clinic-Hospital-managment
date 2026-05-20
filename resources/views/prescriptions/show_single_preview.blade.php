{{-- resources/views/prescriptions/show_single_preview.blade.php --}}
@extends('adminlte::page')

@section('title', 'Prescription Preview')

@section('content_header')
  <h1 class="text-primary">Prescription Preview</h1>
@stop

@section('content')
  @include('prescriptions.tabs.preview', [
      'pid'            => $pid,
      'patientId'      => $patientId,
      'rx'             => $rx,
      'patient'        => $patient,
      'complaints'     => $complaints,
      'diagnoses'      => $diagnoses,
      'investigations' => $investigations,
      'medicines'      => $medicines,
      'clinicAddr'     => $clinicAddr,
      'clinicPhone'    => $clinicPhone,
  ])
@stop
