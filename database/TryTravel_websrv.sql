-- TryTravel. Import into the database selected in phpMyAdmin.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
    role_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id SMALLINT UNSIGNED NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    consent_at DATETIME NOT NULL,
    CONSTRAINT uq_users_email UNIQUE (email),
    KEY ix_users_role (role_id),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id)
        REFERENCES roles (role_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    reset_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    KEY ix_reset_token_hash (token_hash),
    KEY ix_reset_user (user_id),
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_documents (
    document_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(80) NOT NULL,
    series VARCHAR(30) NULL,
    number VARCHAR(40) NOT NULL,
    issued_at DATE NULL,
    expires_at DATE NULL,
    KEY ix_documents_user (user_id),
    CONSTRAINT fk_documents_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT ck_document_dates CHECK (expires_at >= issued_at)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tour_categories (
    category_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    CONSTRAINT uq_category_name UNIQUE (name)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS destinations (
    destination_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    country VARCHAR(100) NOT NULL,
    name VARCHAR(150) NOT NULL,
    KEY ix_destination_country_name (country, name)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tours (
    tour_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id SMALLINT UNSIGNED NOT NULL,
    destination_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    nights SMALLINT UNSIGNED NOT NULL,
    meal_plan VARCHAR(80) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    archived_at DATETIME NULL,
    KEY ix_tours_category_catalog (category_id, archived_at, departure_date),
    KEY ix_tours_destination (destination_id),
    KEY ix_tours_catalog_price (archived_at, price),
    KEY ix_tours_departure (departure_date),
    CONSTRAINT fk_tours_category FOREIGN KEY (category_id)
        REFERENCES tour_categories (category_id),
    CONSTRAINT fk_tours_destination FOREIGN KEY (destination_id)
        REFERENCES destinations (destination_id),
    CONSTRAINT ck_tours_nights CHECK (nights > 0),
    CONSTRAINT ck_tours_price CHECK (price >= 0)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tour_guest_limits (
    tour_id BIGINT UNSIGNED PRIMARY KEY,
    max_adults TINYINT UNSIGNED NOT NULL,
    max_children TINYINT UNSIGNED NOT NULL,
    CONSTRAINT fk_guest_limits_tour FOREIGN KEY (tour_id)
        REFERENCES tours (tour_id) ON DELETE CASCADE,
    CONSTRAINT ck_guest_limits_adults CHECK (max_adults > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    booking_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    tour_id BIGINT UNSIGNED NOT NULL,
    checking TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY ix_bookings_user_created (user_id, created_at),
    KEY ix_bookings_tour (tour_id),
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id)
        REFERENCES users (user_id),
    CONSTRAINT fk_bookings_tour FOREIGN KEY (tour_id)
        REFERENCES tours (tour_id),
    CONSTRAINT ck_booking_checking CHECK (checking IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
    user_id BIGINT UNSIGNED NOT NULL,
    tour_id BIGINT UNSIGNED NOT NULL,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, tour_id),
    KEY ix_favorites_tour (tour_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_tour FOREIGN KEY (tour_id)
        REFERENCES tours (tour_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promotions (
    promotion_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    starts_at DATE NOT NULL,
    ends_at DATE NOT NULL,
    KEY ix_promotions_dates (starts_at, ends_at),
    CONSTRAINT ck_promotions_dates CHECK (ends_at >= starts_at)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promotion_tours (
    promotion_id BIGINT UNSIGNED NOT NULL,
    tour_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (promotion_id, tour_id),
    KEY ix_promotion_tours_tour (tour_id),
    CONSTRAINT fk_promotion_tours_promotion FOREIGN KEY (promotion_id)
        REFERENCES promotions (promotion_id) ON DELETE CASCADE,
    CONSTRAINT fk_promotion_tours_tour FOREIGN KEY (tour_id)
        REFERENCES tours (tour_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tour_reviews (
    review_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tour_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'published', 'rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_tour_review_user UNIQUE (tour_id, user_id),
    KEY ix_tour_reviews_user (user_id),
    KEY ix_tour_reviews_rating (status, tour_id, rating),
    CONSTRAINT fk_tour_reviews_tour FOREIGN KEY (tour_id)
        REFERENCES tours (tour_id) ON DELETE CASCADE,
    CONSTRAINT fk_tour_reviews_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT ck_tour_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


START TRANSACTION;
INSERT INTO roles (role_id, name)
VALUES (1, 'Пользователь'), (2, 'Администратор')
ON DUPLICATE KEY UPDATE role_id = roles.role_id;

INSERT INTO tour_categories (name)
VALUES ('Горящие туры'), ('Новогодние туры')
ON DUPLICATE KEY UPDATE category_id = tour_categories.category_id;
COMMIT;

