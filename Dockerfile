# Gunakan gambar resmi PHP dengan server Apache yang sudah terpasang
FROM php:8.2-apache

# Aktifkan ekstensi yang dibutuhkan untuk terhubung ke MySQL/PostgreSQL
RUN docker-php-ext-install pdo pdo_mysql

# Salin semua file proyek Anda dari komputer ke dalam folder server di dalam container
COPY . /var/www/html/

# (Opsional) Atur izin agar server bisa menulis file jika diperlukan
RUN chown -R www-data:www-data /var/www/html