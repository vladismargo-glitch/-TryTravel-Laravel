@extends('layouts.admin')

@section('heading', 'Пользователи')

@section('content')
    <ul class="admin-list">
        @forelse ($users as $user)
            @php($isSelf = (string) $user->user_id === (string) $adminId)
            <li class="admin-row">
                <div class="admin-row-info">
                    <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
                    <p>{{ $user->email }} @if ($isSelf) · Твой аккаунт @endif</p>
                </div>
                <span @class(['admin-badge', 'admin-badge-active' => (int) $user->role_id === \App\Models\User::ADMIN_ROLE])>{{ $user->role }}</span>
                @unless ($isSelf)
                    <a class="admin-button" href="{{ route('admin.users.role.edit', $user->user_id) }}">Изменить роль</a>
                @endunless
            </li>
        @empty
            <li class="admin-empty">Пользователей пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination', ['tours' => $users, 'paginationLabel' => 'Страницы списка пользователей'])
@endsection
