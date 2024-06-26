-- Создание базы данных и таблиц
CREATE DATABASE IF NOT EXISTS test_zadanie;

USE test_zadanie;

-- Создание таблицы для записей блога
CREATE TABLE IF NOT EXISTS posts
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    title  VARCHAR(255),
    body   TEXT
);

-- Создание таблицы для комментариев
CREATE TABLE IF NOT EXISTS comments
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    postId INT,
    name   VARCHAR(255),
    email  VARCHAR(255),
    body   TEXT
);
