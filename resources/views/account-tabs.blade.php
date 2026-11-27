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
    @can('manage-bookings')
        <a class="account-tab" href="{{ route('admin.bookings.index') }}">
            <img src="{{ asset('images/plan.png') }}" alt="" class="account-tab-icon">
            <span>Заявки</span>
        </a>
    @endcan
    @can('manage-promotions')
        <a class="account-tab" href="{{ route('admin.promotions.index') }}">
            <img src="{{ asset('images/plan.png') }}" alt="" class="account-tab-icon">
            <span>Управление акциями</span>
        </a>
    @endcan
    @can('manage-roles')
        <a class="account-tab" href="{{ route('admin.users.index') }}">
            <img src="{{ asset('images/Profile.png') }}" alt="" class="account-tab-icon">
            <span>Пользователи</span>
        </a>
    @endcan
    @can('moderate-reviews')
        <a class="account-tab" href="{{ route('admin.reviews.index') }}">
            <img src="{{ asset('images/Star.png') }}" alt="" class="account-tab-icon">
            <span>Отзывы</span>
        </a>
    @endcan
</nav>
