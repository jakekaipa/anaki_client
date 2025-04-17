FROM php:8.2.20-fpm

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 시스템 의존성 및 PHP 확장 설치
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip exif

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 애플리케이션 파일 복사
# COPY . .

# 권한 설정
# RUN chown -R www-data:www-data /var/www/html \
#     && chmod -R 755 /var/www/html/storage

# www-data 사용자 및 그룹 ID 변경 (호스트 시스템의 ID와 일치하도록 조정)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# 권한 설정
# RUN chown -R www-data:www-data /var/www/html \
#     && find /var/www/html -type f -exec chmod 644 {} \; \
#     && find /var/www/html -type d -exec chmod 755 {} \; \
#     && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
#     && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# PHP-FPM 설정 최적화 (선택사항)
RUN echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
       && echo "pm.start_servers = 5" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
       && echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
       && echo "pm.max_spare_servers = 35" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
       && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
       && echo "php_admin_value[error_log] = /dev/stderr" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# 포트 노출
EXPOSE 9000

CMD ["php-fpm"]
