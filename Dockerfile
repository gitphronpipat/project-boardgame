FROM php:8.1-apache

# 1. ตั้งค่า Environment เป็น production
ENV CI_ENV=production

# 2. ปิดการแสดง error/warning บนหน้าเว็บ production
RUN echo "display_errors = Off\nerror_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT\nlog_errors = On" > /usr/local/etc/php/conf.d/ci3-production.ini

# 3. เปิดใช้งาน mod_rewrite และ headers สำหรับ CodeIgniter 3 (.htaccess + HTTPS proxy)
RUN a2enmod rewrite headers \
    && echo 'SetEnvIf X-Forwarded-Proto "^https$" HTTPS=on' >> /etc/apache2/apache2.conf

# 4. อนุญาตให้ .htaccess ทำงาน (AllowOverride All เพื่อรองรับ clean URL)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 5. ติดตั้ง extensions ที่จำเป็นสำหรับเชื่อมต่อ Firebase (curl)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# 6. คัดลอกโปรเจกต์ไปยัง Web Root ของ Apache
WORKDIR /var/www/html
COPY . /var/www/html/

# 7. กำหนดสิทธิ์โฟลเดอร์สำหรับ Apache และ Session/Logs/Cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/application/logs \
    && chmod -R 777 /var/www/html/application/cache

# 8. เปิด Port 80 สำหรับ Web Service
EXPOSE 80

CMD ["apache2-foreground"]
