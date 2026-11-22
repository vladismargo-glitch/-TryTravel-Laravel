@extends('layouts.auth')

@section('heading', 'Вход в личный кабинет')

@section('content')
<form class="auth-form" action="{{ route('login.submit') }}" method="post">
    @csrf
    <div class="auth-group">
        <input class="auth-input" type="email" id="login-email" name="email" value="{{ old('email') }}" placeholder="E-mail" required>
    </div>

    <div class="auth-group">
        <div class="auth-password-wrap">
            <input class="auth-input" type="password" id="login-password" name="password" placeholder="Пароль" required>
            <button type="button" class="auth-toggle-password" data-toggle="login-password">Показать</button>
        </div>
    </div>

    <div class="auth-forgot">
        <a href="{{ route('password.request') }}">Забыли пароль?</a>
    </div>
    <button type="submit" class="auth-submit">Вход</button>
</form>

<p class="auth-alt-text">
    Нет аккаунта? <a href="{{ route('register') }}" class="auth-link">Зарегистрироваться</a>
</p>
@endsection
