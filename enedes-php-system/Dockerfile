FROM php:8.1-apache

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar todos os arquivos para o diretório web do Apache
COPY . /var/www/html/

# Definir permissões corretas
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expor porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]

