@extends('layouts.admin')

@section('heading', 'Изменение роли')

@section('content')
    <p class="auth-feedback">{{ $user->first_name }} {{ $user->last_name }} · {{ $user->email }}</p>

    <form class="auth-form" method="post" action="{{ route('admin.users.role.update', $user->user_id) }}">
        @csrf
        @method('PATCH')

        @php($selectedRole = old('role_id', $user->role_id))
        <div class="auth-group">
            <label class="auth-label" for="user-role">Роль</label>
            <select class="auth-input" id="user-role" name="role_id" required
                @if ($errors->has('role_id')) aria-invalid="true" @endif>
                @foreach ($roles as $role)
                    <option value="{{ $role->role_id }}"
                        @selected(is_scalar($selectedRole) && (string) $role->role_id === (string) $selectedRole)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="auth-group">
            <label class="auth-label" for="role-password">Твой текущий пароль</label>
            <input class="auth-input" id="role-password" name="current_password" type="password" maxlength="72"
                autocomplete="current-password" required @if ($errors->has('current_password')) aria-invalid="true" @endif>
        </div>
        <button class="auth-submit" type="submit">Сохранить роль</button>
    </form>

    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.users.index') }}">К списку пользователей</a></p>
@endsection
