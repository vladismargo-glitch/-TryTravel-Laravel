@extends('layouts.auth')

@section('heading', 'Восстановление пароля')

@section('content')
<p class="auth-feedback">Укажи электронную почту, которую использовал при регистрации.</p>

<form class="auth-form" action="{{ route('password.email') }}" method="post">
    @csrf
    <div class="auth-group">
        <label for="reset-email">Электронная почта</label>
        <input class="auth-input" type="email" id="reset-email" name="email"
            value="{{ old('email') }}" maxlength="254" autocomplete="email" required>
    </div>
    <button type="submit" class="auth-submit">Получить ссылку</button>
</form>

<p class="auth-alt-text"><a href="{{ route('login') }}" class="auth-link">Вернуться ко входу</a></p>
@endsection
