@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Login')

@section('auth_body')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="input-group mb-3">
        <input type="text" name="mobile" class="form-control" placeholder="mobile" required autofocus>
    </div>
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Mes</label>
            </div>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
    </div>
</form>
@endsection
