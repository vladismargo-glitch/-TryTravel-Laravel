<nav class="account-tabs" aria-label="Личный кабинет">
    @foreach ([
        'account' => ['Мои туры', 'plan.png'],
        'profile.edit' => ['Профиль', 'Profile.png'],
        'documents' => ['Документы', 'kopi.png'],
        'favorites.index' => ['Избранное', 'izbrannoe.png'],
        'logout' => ['Выйти', 'vihod.png'],
    ] as $tab => [$label, $icon])
        <a href="{{ route($tab) }}" @class(['account-tab', 'active' => request()->routeIs($tab)])
            @if (request()->routeIs($tab)) aria-current="page" @endif>
            <img src="{{ asset('images/'.$icon) }}" alt="" class="account-tab-icon">
            <span>{{ $label }}</span>
        </a>
    @endforeach
    @can('manage-catalog')
        <a class="account-tab" href="{{ route('admin.tours.index') }}">
            <img src="{{ asset('images/plan.png') }}" alt="" class="account-tab-icon">
            <span>Управление турами</span>
        </a>
    @endcan
</nav>
