@extends('layouts.app')

@section('title', 'Organizer')

@section('body_full')
<div class="container login-window">
    <div class="row mb-3">
        <div class="col">
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                @auth
                    <div class="mb-3">
                        You are logged in as {{ auth()->user()->email }}, <a href="#" onclick="document.getElementById('logout-form').submit()">Logout</a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
                    </div>
                @endauth

                <div class="mb-3">
                    <input type="email" value="{{ old('email', $last_username ?? '') }}" name="email" id="inputEmail" class="form-control" placeholder="Email" autocomplete="email" required autofocus>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" autocomplete="current-password" required>
                </div>

                <button class="btn btn-primary" type="submit">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
