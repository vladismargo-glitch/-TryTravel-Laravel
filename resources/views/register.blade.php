<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryTravel - Регистрация аккаунта</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="/css/trytravel-auth.css">
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
                <li><a href="{{ route('tours.index') }}">Каталог туров</a></li>
                <li><a href="hot-tours.html">Горящие туры</a></li>
                <li><a href="promotions.html">Акции</a></li>
                <li><a href="newyear-tours.html">Новогодние туры</a></li>
                <li><a href="about.html">О нас</a></li>
                <li><a href="contacts.html">Контакты</a></li>
                <li><a href="favorites.html" class="favorites-icon">Избранное</a></li>
            </ul>
        </div>
    </nav>

    <div class="breadcrumbs">
        <a href="index.html">Главная</a> / <span class="current">Регистрация аккаунта</span>
    </div>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-box">
                <h1 class="auth-title">Регистрация аккаунта</h1>

                
                @if ($errors->any())
                    <ul role="alert" class="auth-feedback auth-error">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @if (session('status'))
                    <p role="status" class="auth-feedback">{{ session('status') }}</p>
                @endif
                <form class="auth-form" action="{{ route('register.submit') }}" method="post">
                    @csrf
                    <div class="auth-group">
                        <input class="auth-input" type="text" id="reg-last-name" name="lastName" value="{{ old('lastName') }}" placeholder="Фамилия" required>
                    </div>

                    <div class="auth-group">
                        <input class="auth-input" type="text" id="reg-first-name" name="firstName" value="{{ old('firstName') }}" placeholder="Имя" required>
                    </div>

                    <div class="auth-group">
                        <input class="auth-input" type="email" id="reg-email" name="email" value="{{ old('email') }}" placeholder="E-mail" required>
                    </div>

                    <div class="auth-group">
                        <input class="auth-input" type="tel" id="reg-phone" name="phone" value="{{ old('phone') }}" placeholder="Номер телефона" required>
                    </div>

                    <div class="auth-group">
                        <div class="auth-password-wrap">
                            <input class="auth-input" type="password" id="reg-password" name="password"
                                placeholder="Пароль" required minlength="8" pattern="(?=.*\p{L})(?=.*\p{Nd}).{8,}"
                                autocomplete="new-password" aria-describedby="reg-password-requirement">
                            <button type="button" class="auth-toggle-password" data-toggle="reg-password">Показать</button>
                        </div>
                        <small id="reg-password-requirement">Не менее 8 символов, включая букву и цифру</small>
                    </div>

                    <label class="auth-agree">
                        <input type="checkbox" name="agree" value="1" @checked(old('agree')) required>
                        <span>Я даю согласие на обработку персональных данных для создания учётной записи.</span>
                    </label>

                    <button type="submit" class="auth-submit">Регистрация</button>
                </form>

                <p class="auth-alt-text">
                    Уже есть аккаунт? <a href="login.html" class="auth-link">Войти в аккаунт</a>
                </p>
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
                    <li><a href="about.html">О нас</a></li>
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
                    <span>Томск, ул. Прем, 15</span>
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
            2025 TryTravel. Все права защищены.
        </div>
    </footer>
</body>
</html>
