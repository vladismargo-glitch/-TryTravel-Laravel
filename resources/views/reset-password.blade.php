@extends('layouts.auth')

@section('heading', 'Новый пароль')

@section('content')
<form class="auth-form" action="{{ route('password.update', ['resetId' => $resetId]) }}" method="post">
    @csrf
    <div class="auth-group">
        <label for="new-password">Новый пароль</label>
        <input class="auth-input" type="password" id="new-password" name="password"
            minlength="8" pattern="(?=.*\p{L})(?=.*\p{Nd}).{8,}"
            autocomplete="new-password" aria-describedby="password-requirement" required>
        <small id="password-requirement">Не менее 8 символов, включая букву и цифру</small>
    </div>
    <div class="auth-group">
        <label for="password-confirmation">Повтори новый пароль</label>
        <input class="auth-input" type="password" id="password-confirmation"
            name="password_confirmation" minlength="8" autocomplete="new-password" required>
    </div>
    <button type="submit" class="auth-submit">Сохранить пароль</button>
</form>
@endsection
