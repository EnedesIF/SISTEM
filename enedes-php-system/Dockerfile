FROM php:8.1-apache

# Instalar extensões PHP necessárias e dependências do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar Apache para reconhecer index.php e index.html
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Copiar o código do sistema para o diretório padrão do Apache
COPY enedes-php-system /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
