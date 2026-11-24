<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryTravel - Документы</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/personal-account.css">
    <link rel="stylesheet" href="css/documents.css">
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
                <li><a href="hot-tours.html">Горящие туры</a></li>
                <li><a href="promotions.html">Акции</a></li>
                <li><a href="newyear-tours.html">Новогодние туры</a></li>
                <li><a href="about.html">О нас</a></li>
                <li><a href="contacts.html">Контакты</a></li>
                <li><a href="{{ route('favorites.index') }}" class="favorites-icon">Избранное</a></li>
            </ul>
        </div>
    </nav>

    <div class="breadcrumbs">
        <a href="index.html">Главная</a> / <a href="personal-account.html">Личный кабинет</a> / Документы
    </div>

    <section class="section personal-account-section">
        <h2 class="section-title">Документы</h2>
        
        @include('account-tabs')

        <div class="documents-content">
            <div class="profile-section">
                <h3 class="profile-section-title">Контактные данные профиля</h3>
                
                <div class="contact-field">
                    <div class="contact-field-row">
                        <span class="contact-label">Телефон</span>
                        <div class="contact-value">{{ auth()->user()->phone }}</div>
                    </div>
                    <p class="contact-hint">Для бронирования и оперативной связи по вопросам брони</p>
                </div>

                <div class="contact-field">
                    <div class="contact-field-row">
                        <span class="contact-label">Электронная почта</span>
                        <div class="contact-value">{{ auth()->user()->email }}</div>
                    </div>
                    <p class="contact-hint"><a href="{{ route('profile.edit') }}">Изменить контактные данные</a></p>
                </div>
            </div>

            <section class="document-section">
                <div class="document-header">
                    <h3 class="profile-section-title">Мои документы</h3>
                    <a class="add-button" href="{{ route('documents.create') }}">Добавить документ</a>
                </div>

                @if (session('document_status'))
                    <p role="status">{{ session('document_status') }}</p>
                @endif

                @forelse ($documents as $document)
                    <article class="document-card">
                        <h4>{{ $document->document_type }}</h4>
                        <dl>
                            @if ($document->series !== null)
                                <dt>Серия</dt>
                                <dd>{{ $document->series }}</dd>
                            @endif
                            <dt>Номер</dt>
                            <dd>{{ $document->number }}</dd>
                            @if ($document->issued_at)
                                <dt>Дата выдачи</dt>
                                <dd>{{ \Illuminate\Support\Carbon::parse($document->issued_at)->format('d.m.Y') }}</dd>
                            @endif
                            @if ($document->expires_at)
                                <dt>Действителен до</dt>
                                <dd>{{ \Illuminate\Support\Carbon::parse($document->expires_at)->format('d.m.Y') }}</dd>
                            @endif
                        </dl>
                        <div class="document-actions">
                            <a href="{{ route('documents.edit', $document->document_id) }}">Редактировать</a>
                            <a href="{{ route('documents.confirm-delete', $document->document_id) }}">Удалить</a>
                        </div>
                    </article>
                @empty
                    <p>У тебя пока нет сохранённых документов.</p>
                @endforelse
            </section>
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

