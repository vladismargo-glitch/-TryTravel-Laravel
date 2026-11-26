# TryTravel

Laravel 13 и PHP 8.3+. Публичные страницы и формы используют вёрстку первого курсового. Регистрация, вход, выход и восстановление пароля работают через базу данных. Страницы личного кабинета доступны после входа.

## Локальный запуск

1. Запустите MySQL 8.0.16+ или MariaDB 10.11. Импортируйте `database/TryTravel.sql` пользователем базы данных с правом создания схемы.
2. Скопируйте `.env.example` в `.env`. Заполните `DB_USERNAME` и `DB_PASSWORD` реквизитами локальной учётной записи, у которой есть доступ на чтение и запись в БД `trytravel`.
3. Выполните `composer install --no-dev` и `php artisan key:generate`.
4. Выполните `php artisan serve --host=127.0.0.1 --port=8000` и откройте [TryTravel](http://127.0.0.1:8000/). Сборка фронтенда не требуется.

Схема создаётся SQL-файлом. Значение `bookings.checking` равно 1 для подтверждённой брони или 0 при отсутствии подтверждения. Для регистрации назначается роль с `role_id = 1`.

## Проверка восстановления пароля

На странице входа нажмите **Забыли пароль?** и введите адрес зарегистрированного аккаунта. При локальной настройке `MAIL_MAILER=log` письмо со ссылкой появится в `storage/logs/laravel.log`. Откройте ссылку из последнего письма для этого адреса. Она действует 30 минут и позволяет сменить пароль один раз.

Для доставки на почту на сервере укажите адрес сайта в `APP_URL` и настройте `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD` и `MAIL_FROM_ADDRESS` по данным почтового сервера. Храните настройки подключения в локальном `.env`.

## Поиск туров

После импорта схемы можно добавить демонстрационные предложения командой `php artisan db:seed --class=TourCatalogSeeder`. Она переносит карточки из макета TryTravel с демонстрационными датами и сохраняет существующие записи.

Характеристики отелей в дополнительных карточках сверены с официальными страницами [Hilton в Стамбуле](https://www.hilton.com/en/locations/turkiye/istanbul/), [Hilton в Дубае](https://www.hilton.com/en/p/discover-travel/emea/dubai/), [Marriott на Пхукете](https://www.marriott.com/en-us/destinations/thailand/phuket.mi) и [Marriott на Красном море](https://www.emea.marriott.com/en/travel-tips/the-most-unforgettable-resorts-on-egypts-red-sea-coast). Условия пакетов вымышлены для демонстрации.

Фото направлений подготовлены из материалов Wikimedia Commons. Размер и формат файлов были изменены:

- `demo-istanbul.webp` — [вид с Босфора, Sourabh.biswas003](https://commons.wikimedia.org/wiki/File:PXL_20241210_122905199.MP_Istanbul_Cityscape_from_Bosphorus_Strait_Cruise_T%C3%BCrkiye_28.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0/).
- `demo-red-sea.webp` — [Красное море около Хургады, Rebeccananonof](https://commons.wikimedia.org/wiki/File:Red_Sea_near_Hurghada,_Egypt.png), [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/).

- `demo-alanya-cleopatra.webp` — [Dosseman](https://commons.wikimedia.org/wiki/File:Alanya_Cleopatra_Beach_View_to_Citadel_5831.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-alanya-harbour.webp` — [Emineakl42](https://commons.wikimedia.org/wiki/File:Alanya_K%C4%B1z%C4%B1l_Kulesi.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-ras-al-khaimah.webp` — [Tashmetova808](https://commons.wikimedia.org/wiki/File:Sea_view,_Marjan_island,_Ras_al_Khaimah,_UAE.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-phuket-karon.webp` — [Scorewith German](https://commons.wikimedia.org/wiki/File:Thailand_Phuket_Karon_Beach_-_panoramio.jpg), [CC BY 3.0](https://creativecommons.org/licenses/by/3.0).
- `demo-hurghada-marina.webp` — [Michael Botros](https://commons.wikimedia.org/wiki/File:Hurghada_Yacht_Marina.jpg), [CC BY 4.0](https://creativecommons.org/licenses/by/4.0).
- `demo-istanbul-kadikoy.webp` — [Muneeb h.hashmi](https://commons.wikimedia.org/wiki/File:Sunset_view_in_Kadikoy.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-dubai-marina.webp` — [Khanshahab06](https://commons.wikimedia.org/wiki/File:Dubai_Marina_Skyline_93.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-phuket-patong.webp` — [Arnaud-Victor Monteux](https://commons.wikimedia.org/wiki/File:Long-tail_boats_on_Patong_Beach_Phuket.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-egypt-soma-bay.webp` — [dronepicr](https://commons.wikimedia.org/wiki/File:Beach_of_Soma_Bay,_Egypt_%2851223045581%29.jpg), [CC BY 2.0](https://creativecommons.org/licenses/by/2.0).
- `demo-istanbul-ortakoy.webp` — [Caiuscamargarus](https://commons.wikimedia.org/wiki/File:Ortakoy.jpg), [CC BY-SA 3.0](https://creativecommons.org/licenses/by-sa/3.0).
- `demo-istanbul-galata.webp` — [Alexxx Malev](https://commons.wikimedia.org/wiki/File:Istanbul_Galata_Tower_%28236663843%29.jpeg), [CC BY-SA 3.0](https://creativecommons.org/licenses/by-sa/3.0).
- `demo-dubai-palm.webp` — [giggel](https://commons.wikimedia.org/wiki/File:Vereinigte_Arabische_Emirate_-_Dubai_-_Palm_Jumeirah_und_Dubai_Marina_-_im_Vordergrund_das_Hotel_Atlantis_-_panoramio.jpg), [CC BY 3.0](https://creativecommons.org/licenses/by/3.0).
- `demo-dubai-coast.webp` — [pe-sa](https://commons.wikimedia.org/wiki/File:Dubai_Jumeirah_Beach.JPG), [CC BY 3.0](https://creativecommons.org/licenses/by/3.0).
- `demo-phuket-mai-khao.webp` — [Dennis Wong](https://commons.wikimedia.org/wiki/File:Sunset_at_Mai_Khao_Beach.jpg), [CC BY 2.0](https://creativecommons.org/licenses/by/2.0).
- `demo-phuket-nai-yang.webp` — [Wolfgang Holzem](https://commons.wikimedia.org/wiki/File:Nai_Yang_Beach_%2838%29.jpg), [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0).
- `demo-egypt-el-gouna.webp` — [Marc Ryckaert](https://commons.wikimedia.org/wiki/File:El_Gouna_R23.jpg), [CC BY 3.0](https://creativecommons.org/licenses/by/3.0).
- `demo-egypt-sharm.webp` — [74papa](https://commons.wikimedia.org/wiki/File:Naama_Bay,_Sharm_el_Sheikh,_Egypt.jpg), [CC BY 4.0](https://creativecommons.org/licenses/by/4.0).

Форма на главной странице ищет по всему каталогу. На страницах «Горящие туры» и «Новогодние туры» поиск работает в выбранном разделе. Введите часть названия, страну или курорт, например «Пхукет» или «Дубай».

Поиск можно ограничить периодом вылета и составом туристов. В режиме «Отели» текст сопоставляется с названием тура и курортом, а в режиме «Путешествия» — со страной и курортом. В «Турах» поиск идёт по всем трём полям. Ограничения по составу туристов задаются при редактировании тура. Если эти сведения не заполнены, тур остаётся доступен в поиске с вариантом «Любой состав».

Для ранее развёрнутой базы повторно импортируйте актуальный SQL, чтобы создать таблицу `tour_guest_limits`. На `websrv` импортируйте `database/TryTravel_websrv.sql` в выбранную базу phpMyAdmin.

Сортировка сохраняет поисковый запрос и выбранный раздел. «По популярности» выводит сначала туры с большим числом подтверждённых заявок (`bookings.checking = 1`). «По отзывам» использует среднюю оценку опубликованных отзывов и их количество при равной оценке. Туры без опубликованных отзывов идут после туров с оценками.

Если база была создана до добавления отзывов, повторно импортируйте обновлённую схему. Для phpMyAdmin используйте `database/TryTravel_websrv.sql` из выбранной базы. Скрипт добавляет таблицу `tour_reviews` и сохраняет существующие записи.
