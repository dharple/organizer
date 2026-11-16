@extends('layouts.app')

@section('title', 'Organizer')

@section('body_full')
<div class="container login-window">
    <div class="row mb-3">
        <div class="col">
            @auth
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="mb-3">
                    @csrf
                    You are logged in as {{ auth()->user()->email }}, <button type="submit" class="btn btn-link p-0 align-baseline">Logout</button>
                </form>
            @endauth

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

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
