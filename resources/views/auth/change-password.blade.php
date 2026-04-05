@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
  <h1>Change Password</h1>
@endsection

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      @method('PUT')

      <div class="form-group mb-3">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label>New Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>

      <button class="btn btn-primary">Update Password</button>
    </form>
  </div>
</div>

@endsection
