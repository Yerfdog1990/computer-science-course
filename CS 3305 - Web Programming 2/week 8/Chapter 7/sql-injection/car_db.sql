

-- Create the database
CREATE DATABASE IF NOT EXISTS car_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE car_db;

-- Create the cars table
CREATE TABLE IF NOT EXISTS cars (
    id       INT          NOT NULL AUTO_INCREMENT,
    make     VARCHAR(100) NOT NULL,
    model    VARCHAR(100) NOT NULL,
    year     YEAR         NOT NULL,
    color    VARCHAR(50)  NULL,
    price    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


