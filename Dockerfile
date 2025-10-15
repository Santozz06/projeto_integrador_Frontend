# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Atualiza a lista de pacotes e instala dependências necessárias
RUN apt-get update && \
    apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        unzip \
        default-mysql-client && \
    # Instala as extensões do PHP
    docker-php-ext-install pdo pdo_mysql mysqli && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-enable opcache && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos de configuração do PHP
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copia os arquivos do seu projeto para o diretório padrão do Apache
COPY ./backend/dashtreme-master /var/www/html

# Define permissões adequadas
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exponha a porta 80
EXPOSE 80