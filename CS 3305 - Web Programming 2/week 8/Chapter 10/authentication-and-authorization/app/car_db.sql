USE car_db;

-- Users table with hashed passwords and roles
CREATE TABLE IF NOT EXISTS users (
                                     id            INT          NOT NULL AUTO_INCREMENT,
                                     name          VARCHAR(100) NOT NULL,
                                     email         VARCHAR(150) NOT NULL,
                                     password_hash VARCHAR(255) NOT NULL,
                                     role          ENUM('admin', 'member', 'guest') NOT NULL DEFAULT 'guest',
                                     created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                     PRIMARY KEY (id),
                                     UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- API keys table
CREATE TABLE IF NOT EXISTS api_keys (
                                        id         INT         NOT NULL AUTO_INCREMENT,
                                        user_id    INT         NOT NULL,
                                        api_key    VARCHAR(64) NOT NULL,
                                        created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        PRIMARY KEY (id),
                                        UNIQUE KEY uq_api_key (api_key),
                                        CONSTRAINT fk_api_keys_user FOREIGN KEY (user_id)
                                            REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed test users (passwords are bcrypt hashes of 'password123')
INSERT INTO users (name, email, password_hash, role) VALUES
                                                         ('Alice Admin', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
                                                         ('Bob Member',  'bob@example.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member'),
                                                         ('Carol Guest', 'carol@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'guest');