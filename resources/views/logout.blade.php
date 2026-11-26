<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryTravel - Выход</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/logout.css">
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
        <a href="index.html">Главная</a> / Личный кабинет / Выход
    </div>

    <section class="section logout-section">
        <div class="logout-container">
            <div class="logout-box">
                <div class="logout-icon-wrapper">
                    <img src="images/Open Door.png" alt="Выход" class="logout-icon">
                </div>
                <h2 class="logout-title">Вы уверены, что хотите выйти?</h2>
                <p class="logout-description">После выхода вам потребуется снова войти в личный кабинет для доступа к вашим заказам и настройкам профиля.</p>
                
                <div class="logout-buttons">
                    <form action="{{ route('logout.submit') }}" method="post">
                        @csrf
                        <button type="submit" class="logout-btn confirm-btn">Да, выйти</button>
                    </form>
                    <a href="personal-account.html" class="logout-btn cancel-btn">Отмена</a>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="newsletter-content">
            <h2 class="section-title">Получите эксклюзивные предложения</h2>
            <p class="newsletter-description">
                Подпишитесь на нашу рассылку и первыми получайте информацию о спец.акциях и новых направлениях.
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

