@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Register')

@section('auth_body')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="input-group mb-3">
        <input type="text" name="name" class="form-control" placeholder="Full name" required autofocus>
    </div>
    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="input-group mb-3">
        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Register</button>
</form>
@endsection
