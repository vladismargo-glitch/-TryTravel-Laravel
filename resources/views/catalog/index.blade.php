<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryTravel — {{ $heading }}</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="{{ asset($section === 'newyear' ? 'css/newyear-tours.css' : 'css/hot-tours.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tour-search.css') }}">
</head>
<body>
    <div class="top-bar">
        <div class="top-bar-content">
            <a href="index.html" class="top-bar-logo" style="text-decoration: none; color: inherit;">
                <div class="logo-icon">T</div>
                <span>TryTravel</span>
            </a>
            <div class="top-bar-contacts">
                <span class="contacts-item">Для туристов: 8 (495) 775-00-00</span>
                <span class="contacts-sep">/</span>
                <span class="contacts-item">Для турагентов: 8 (495) 933-55-77</span>
            </div>
            <a href="personal-account.html" class="personal-cabinet">
                <img src="images/image1.png.png" alt="Личный кабинет" class="personal-cabinet-icon">
                <span>Личный кабинет</span>
            </a>
        </div>
    </div>

    <nav class="navbar">
        <div class="nav-content">
            <ul class="nav-links">
                <li><a href="{{ route('tours.index') }}" @if ($section === 'all') class="catalog-nav-current" aria-current="page" @endif>Каталог туров</a></li>
                <li><a href="hot-tours.html" @if ($section === 'hot') class="catalog-nav-current" aria-current="page" @endif>Горящие туры</a></li>
                <li><a href="promotions.html">Акции</a></li>
                <li><a href="newyear-tours.html" @if ($section === 'newyear') class="catalog-nav-current" aria-current="page" @endif>Новогодние туры</a></li>
                <li><a href="about.html">О нас</a></li>
                <li><a href="contacts.html">Контакты</a></li>
                <li><a href="favorites.html" class="favorites-icon">Избранное</a></li>
            </ul>
        </div>
    </nav>

    <div class="breadcrumbs">
        <a href="index.html">Главная</a> / {{ $heading }}
    </div>

    <main>
        @if ($section === 'hot')
            @include('catalog.hot-intro')
        @elseif ($section === 'newyear')
            @include('catalog.newyear-intro')
        @else
            <div class="hot-tours-header"><h1>{{ $heading }}</h1></div>
        @endif

        <section class="catalog-search" aria-label="Поиск по каталогу">
            @include('catalog.favorite-status')
            <form class="catalog-search-form" action="{{ $searchUrl }}" method="get" role="search">
                <label for="catalog-query">Название тура, курорт или страна</label>
                <div class="catalog-search-fields">
                    <input type="search" id="catalog-query" name="q" value="{{ $search }}"
                        maxlength="100" placeholder="Например, Пхукет">
                    <select id="catalog-sort" name="sort" aria-label="Сортировка туров">
                        <option value="">По умолчанию</option>
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="tour-select-btn">Применить</button>
                    @if ($hasFilters || $sort !== '' || $scope !== 'all')
                        <a href="{{ $searchUrl }}">Сбросить</a>
                    @endif
                </div>
                <div class="catalog-search-fields catalog-search-filters">
                    <select name="scope" aria-label="Тип поиска">
                        @foreach ($scopeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($scope === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label>Вылет от <input type="date" name="departure_from" value="{{ $filters['departure_from'] ?? '' }}"></label>
                    <label>Вылет до <input type="date" name="departure_to" value="{{ $filters['departure_to'] ?? '' }}"></label>
                    <select name="guests" aria-label="Состав туристов">
                        <option value="">Любой состав</option>
                        @foreach ($guestOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['guests'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            @if ($hasFilters)
                <p class="catalog-result-count">Найдено предложений: {{ $tours->total() }}</p>
            @endif
        </section>

        <div class="tours-grid">
            @forelse ($tours as $tour)
                @include('catalog.card', ['tour' => $tour])
            @empty
                <p class="catalog-empty">{{ $hasFilters ? 'По этому запросу туры не найдены.' : 'Здесь пока нет туров.' }}</p>
            @endforelse
        </div>

        @include('catalog.pagination')
    </main>

    <section class="newsletter-section">
        <div class="newsletter-content">
            <h2 class="section-title">Получите эксклюзивные предложения</h2>
            <p class="newsletter-description">
                Подпишитесь на нашу рассылку и первыми получайте информацию о спец акциях и новых направлениях.
            </p>
            <form class="newsletter-form">
                <input type="email" placeholder="Ваш email" required>
                <button type="submit">Подписаться</button>
            </form>
            <div class="newsletter-checkboxes">
                <label class="newsletter-checkbox">
                    <input type="checkbox" required>
                    <span>Даю согласие на обработку персональных данных</span>
                </label>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div>
                <div class="footer-logo">TryTravel</div>
                <p class="footer-description">
                    Премиальные путешествия по всему миру с 2010 года. Мы создаем незабываемые впечатления для наших клиентов.
                </p>
                <div class="social-icons">
                    <div class="social-icon">
                        <img src="images/max-messenger-sign-logo 1.png" alt="WhatsApp">
                    </div>
                    <div class="social-icon">
                        <img src="images/image 211.png" alt="VK">
                    </div>
                    <div class="social-icon">
                        <img src="images/image 212 (1).png" alt="OK">
                    </div>
                    <div class="social-icon">
                        <img src="images/rutube-sign-logo 1.png" alt="Rutube">
                    </div>
                </div>
            </div>
            <div class="footer-column">
                <h4>Направления</h4>
                <ul class="footer-links">
                    <li><a href="#">Европа</a></li>
                    <li><a href="#">Азия</a></li>
                    <li><a href="#">Америка</a></li>
                    <li><a href="#">Африка</a></li>
                    <li><a href="#">Океания</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Компания</h4>
                <ul class="footer-links">
                    <li><a href="#">О нас</a></li>
                    <li><a href="#">Команда</a></li>
                    <li><a href="#">Вакансии</a></li>
                    <li><a href="#">Партнеры</a></li>
                    <li><a href="#">Блог</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Контакты</h4>
                <div class="footer-contact-item">
                    <img src="images/Place Marker.png" alt="Location" style="width: 20px; height: 20px; object-fit: contain;">
                    <span>Томск, ул. Прем. 15</span>
                </div>
                <div class="footer-contact-item">
                    <img src="images/Phone.png" alt="Phone" style="width: 20px; height: 20px; object-fit: contain;">
                    <span>+7(148) 833-72-28</span>
                </div>
                <div class="footer-contact-item">
                    <img src="images/Circled Envelope.png" alt="Email" style="width: 20px; height: 20px; object-fit: contain;">
                    <span>ist@gmail.com</span>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            2025 Name. Все права защищены.
        </div>
    </footer>

</body>
</html>
