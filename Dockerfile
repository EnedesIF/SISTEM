FROM php:8.1-apache

# Instalar dependências PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite Apache
RUN a2enmod rewrite

# Configurar DirectoryIndex para aceitar index.html e index.php
RUN echo "DirectoryIndex index.html index.php" >> /etc/apache2/apache2.conf

# Copiar TODOS os arquivos do projeto para Apache
COPY . /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Expor porta 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2-foreground"]
