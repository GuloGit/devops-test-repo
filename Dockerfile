# Используем официальный образ PHP с Apache
FROM php:7.4-apache

# Устанавливаем рабочую директорию в контейнере
WORKDIR /var/www/html

# Установите необходимые расширения PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем исходный код проекта в контейнер
COPY ./*.php /var/www/html/
COPY ./database/ /var/www/html/

# Создание директории для загруженных фотографий и установка прав доступа
RUN chown -R www-data:www-data /var/www/html/ && \
    chmod -R 755 /var/www/html/

# Открыть порт 8080 для доступа к приложению
EXPOSE 80

# Команда для запуска Apache
CMD ["apache2-foreground"]
