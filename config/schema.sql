SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ============================================================
-- ADMIN USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS admin_users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PERSONS
-- ============================================================
CREATE TABLE IF NOT EXISTS persons (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vorname             VARCHAR(100) NOT NULL,
    rolle_beziehung     VARCHAR(100) DEFAULT NULL,
    bevorzugte_anrede   VARCHAR(100) DEFAULT NULL,
    greeting_text       TEXT         DEFAULT NULL,
    themen              TEXT         DEFAULT NULL,
    gespraechslaenge    ENUM('kurz','mittel','lang') NOT NULL DEFAULT 'mittel',
    aktiv               TINYINT(1)   NOT NULL DEFAULT 1,
    created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_persons_aktiv (aktiv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHOTOS
-- ============================================================
CREATE TABLE IF NOT EXISTS photos (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename          VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime              VARCHAR(50)  NOT NULL,
    checksum          VARCHAR(64)  NOT NULL,
    file_size         INT UNSIGNED NOT NULL DEFAULT 0,
    description       TEXT         DEFAULT NULL,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_photos_checksum (checksum),
    INDEX idx_photos_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PERSON <-> PHOTO (many-to-many)
-- ============================================================
CREATE TABLE IF NOT EXISTS person_photo (
    person_id   INT UNSIGNED NOT NULL,
    photo_id    INT UNSIGNED NOT NULL,
    assigned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (person_id, photo_id),
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (photo_id)  REFERENCES photos(id)  ON DELETE CASCADE,
    INDEX idx_pp_photo (photo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DEVICES
-- ============================================================
CREATE TABLE IF NOT EXISTS devices (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id               VARCHAR(100) NOT NULL UNIQUE,
    api_token               VARCHAR(64)  NOT NULL UNIQUE,
    name                    VARCHAR(100) DEFAULT NULL,
    slideshow_interval_sec  INT UNSIGNED NOT NULL DEFAULT 10,
    face_stable_sec         INT UNSIGNED NOT NULL DEFAULT 4,
    cooldown_sec            INT UNSIGNED NOT NULL DEFAULT 300,
    similarity_threshold    DECIMAL(4,3) NOT NULL DEFAULT 0.650,
    tts_enabled             TINYINT(1)   NOT NULL DEFAULT 1,
    aktiv                   TINYINT(1)   NOT NULL DEFAULT 1,
    last_seen_at            DATETIME     DEFAULT NULL,
    created_at              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_devices_token (api_token),
    INDEX idx_devices_aktiv (aktiv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOGS
-- ============================================================
CREATE TABLE IF NOT EXISTS logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id   VARCHAR(100) NOT NULL,
    person_id   INT UNSIGNED DEFAULT NULL,
    type        VARCHAR(50)  NOT NULL,
    message     TEXT         DEFAULT NULL,
    payload     JSON         DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_device (device_id),
    INDEX idx_logs_person (person_id),
    INDEX idx_logs_type (type),
    INDEX idx_logs_created (created_at),
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED: default admin user
-- ============================================================
-- ============================================================
-- MIGRATION: add description to photos
-- ============================================================
ALTER TABLE photos ADD COLUMN description TEXT DEFAULT NULL AFTER file_size;

-- ============================================================
-- SEED: default admin user
-- ============================================================
INSERT INTO admin_users (username, password) VALUES (
    'admin',
    '$2b$10$vG6P8hE5pvuKwyCfFyByqOMvTkpBL1AJIA9jSgQt3pMArEvGBdP52'
) ON DUPLICATE KEY UPDATE username = username;
