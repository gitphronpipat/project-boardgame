FROM php:8.2-apache

# 1. เปิดใช้งาน mod_rewrite สำหรับ CodeIgniter 3 (.htaccess)
RUN a2enmod rewrite

# 2. อนุญาตให้ .htaccess ทำงาน (AllowOverride All เพื่อรองรับ clean URL)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 3. ติดตั้ง extensions ที่จำเป็นสำหรับเชื่อมต่อ Firebase (curl)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# 4. คัดลอกโปรเจกต์ไปยัง Web Root ของ Apache
WORKDIR /var/www/html
COPY . /var/www/html/

# 5. กำหนดสิทธิ์โฟลเดอร์สำหรับ Apache และ Session/Logs/Cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/application/logs \
    && chmod -R 777 /var/www/html/application/cache

# 6. เปิด Port 80 สำหรับ Web Service
EXPOSE 80

CMD ["apache2-foreground"]
