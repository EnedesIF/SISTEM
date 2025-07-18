FROM php:8.1-apache

# Atualizar e instalar dependências necessárias
RUN apt-get update && apt-get install -y libpq-dev

# Instalar extensões PHP para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar Apache para reconhecer index.php e index.html como index padrão
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Copiar todo o conteúdo da pasta enedes-php-system para /var/www/html
COPY enedes-php-system/ /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
