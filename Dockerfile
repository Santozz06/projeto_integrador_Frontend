# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copia os arquivos do seu projeto para o diretório padrão do Apache
COPY ./backend/dashtreme-master /var/www/html

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Permissões 
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80
EXPOSE 80
