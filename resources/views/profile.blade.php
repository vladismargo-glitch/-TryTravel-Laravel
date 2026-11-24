@extends('layouts.auth')

@section('heading', 'Профиль')

@section('content')
    <form class="auth-form" action="{{ route('profile.update') }}" method="post">
        @csrf
        @method('PATCH')

        @foreach ([
            'firstName' => ['Имя', $user->first_name, 'text', 'given-name', 100],
            'lastName' => ['Фамилия', $user->last_name, 'text', 'family-name', 100],
            'phone' => ['Телефон', $user->phone, 'tel', 'tel', 32],
            'email' => ['Электронная почта', $user->email, 'email', 'email', 254],
        ] as $field => [$label, $savedValue, $type, $autocomplete, $maxLength])
            @php($value = old($field, $savedValue))
            <div class="auth-group">
                <label class="auth-label" for="profile-{{ $field }}">{{ $label }}</label>
                <input class="auth-input" id="profile-{{ $field }}" name="{{ $field }}" type="{{ $type }}"
                    value="{{ is_string($value) ? $value : $savedValue }}" autocomplete="{{ $autocomplete }}"
                    maxlength="{{ $maxLength }}" required>
            </div>
        @endforeach
        <div class="auth-group">
            <label class="auth-label" for="profile-current-password">Текущий пароль</label>
            <input class="auth-input" id="profile-current-password" name="current_password" type="password"
                autocomplete="current-password" maxlength="72" aria-describedby="profile-password-hint">
            <small id="profile-password-hint">Нужен только при смене электронной почты.</small>
        </div>
        <button type="submit" class="auth-submit">Сохранить изменения</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('account') }}">Вернуться в личный кабинет</a></p>
@endsection
