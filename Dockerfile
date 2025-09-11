# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala apenas o essencial para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos do seu projeto para o diretório padrão do Apache
COPY ./backend/dashtreme-master /var/www/html

# Permissões
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80
EXPOSE 80